<?php
namespace App\Services\Shipt;

use App\Enum\Shipt\MercariCsvType;
use App\Enum\ShopPlatform;
use App\Exceptions\api\Shipt\ShipmentOrderException;
use App\Facades\ShiptMethodServ;
use App\Files\Reader\ShiptLogCsvReader;
use App\Models\Shipping;
use App\Services\AbstractCsvService;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Models\Stockpile;
use App\Repositories\Api\Shipt\ShiptLogRepository;
use App\Services\Constant\StockpileHeader;
use App\Services\Notion\NotionService;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * 注文CSVの解析を行うサービスクラス
 */
class ShiptLogCsvService extends AbstractCsvService {

    public function __construct(private ShiptLogRepository $repo) {

    }

    /**
     * 出荷ログ用のCSV読み込みクラスを取得する。
     * @see CsvReader::csvReader
     * @return ShiptLogCsvReader
     */
    protected function csvReader() {
        return new ShiptLogCsvReader;
    }

    /**
     * 注文CSVの内容を解析する。
     * @since 5.1.0
     * @param array $records CSV読み込み結果
     * @return array
    */
    public function parse(array $records) {
        $orders = collect([]);
        $csvCollect = collect($records);
        $orderIds = $csvCollect->pluck(SC::ORDER_ID)->unique();
        logger()->info('注文番号を取得', ['orderIds' => $orderIds->toArray()]);
        $nService = new NotionService();

        $orderIds->each(function($orderId, $index) use ($csvCollect, $orders, $nService) {
            try {
                $nService->hasNotionCard($orderId, $index);
                $orderRow = $this->findRowByOrderId($orderId, $csvCollect, MercariCsvType::ORDER)->sole();
                $productRows = $this->findRowByOrderId($orderId, $csvCollect, MercariCsvType::PRODUCT);
                $address = $productRows->map(function ($item) {
                    return [
                        SC::ZIPCODE => $item[SC::POSTAL_CODE],
                        SC::STATE => $item[SC::STATE],
                        SC::CITY => $item[SC::CITY],
                        SC::ADDRESS_1 => $item[SC::ADDRESS_1],
                        SC::ADDRESS_2 => $item[SC::ADDRESS_2]
                        ];
                })->unique()->first();

                $shiptPrice = $orderRow[SC::SHIPPING_PRICE];
                $platform = ShopPlatform::determine($orderId);
                $subtotal = $orderRow[SC::ORDER_PRICE] + $shiptPrice;

                $fee = ShiptMethodServ::calcMethod($platform, $orderRow, $productRows);
                $grandTotal = $subtotal - $orderRow[SC::DISCOUNT_AMOUNT];

                $items = collect([]);
                $productRows->each (function($item, $index) use ($items, $orderId, $fee, $productRows, $shiptPrice){
                    $stockId = (int)$item[SC::PRODUCT_ID];
                    $pcsPerSet = $this->extractPerSet($item[SC::PRODUCT_NAME]);
                    $this->checkShipment($stockId, $pcsPerSet, $item[SC::QUANTITY], $index);

                    $itemSubtotal = $this->calcItemSubtotal($item, $fee->price, count($productRows), $shiptPrice == 0);
                    $isSet = $pcsPerSet > 0;
                    $rows = [
                        SC::INDEX => $index,
                        StockpileHeader::STOCK => Stockpile::find($stockId),
                        SC::PRODUCT_NAME => $item[SC::PRODUCT_NAME],
                        SC::QUANTITY => $item[SC::QUANTITY],
                        SC::PCS_PER_SET => $pcsPerSet,
                        SC::IS_REGISTERED => $this->repo->existsItem($orderId, $stockId),
                        SC::PRODUCT_PRICE => $item[SC::PRODUCT_PRICE],
                        SC::IS_SET => $isSet,
                        SC::SUBTOTAL => $itemSubtotal,
                        SC::UNIT_PRICE => $this->calcUnitPrice($itemSubtotal, $isSet, $pcsPerSet),
                    ];
                    $items->push($rows);
                });


                $info = collect([SC::INDEX => $index, SC::PLATFORM => ShopPlatform::determine($orderId),
                                                                     SC::ORDER_ID => $orderId, SC::BUYER => $orderRow[SC::BUYER],
                                                                     SC::ITEM_SUBTOTAL => $subtotal,
                                                                     SC::FEE => $fee, SC::GRAND_TOTAL => $grandTotal,
                                                                     SC::DISCOUNT_AMOUNT => $orderRow[SC::DISCOUNT_AMOUNT],
                                                                     SC::ITEMS => $items]);
                $info = $info->merge($address);
                $orders->put($orderId, $info);
            } catch (ShipmentOrderException $e) {
                $this->addError($e->getIndex(), $e->getMsg());
                logger()->error('解析処理に失敗', ['orderId' => $orderId, 'index' => $e->getIndex(), 'error' => $e->getMsg()]);
                return;
            }
            // $row = $this->createRow($index, $r);
            // $orderId = $row->order_id();
            // try {
            //     $nService->hasNotionCard($orderId);
            //     if(!isset($orders[$orderId])){
            //         // 新規生成
            //         $orders[$orderId] = [
            //             SC::ORDER_ID => $orderId,
            //             GC::DATA => $row,
            //         ];
            //     }
            //     // 出荷商品情報チェック
            //     $stockId = (int)$r[SC::PRODUCT_ID];
            //     $isRegistered = $this->repo->existsItem($orderId, $stockId);
            //     if (!$isRegistered) {
            //         $this->checkShipment($stockId, $row->shipment());
            //     }
            //     $stock = Stockpile::find($stockId);

            //     $orders[$orderId][SC::ITEMS][] = [
            //         StockpileHeader::STOCK => $stock,
            //         SC::SHIPMENT => $row->shipment(),
            //         SC::PRODUCT_PRICE => $row->product_price(),
            //         SC::DISCOUNT_AMOUNT => $row->discount(),
            //         SC::IS_REGISTERED => $isRegistered,
            //     ];
            //     } catch (ShipmentOrderException $e) {
            //         $this->addError($row->number(), $e->getMsg());
            //         continue;
            //     }
        });
        logger()->info('注文番号ごとの購入者情報', [ $orders->values()->toArray() ]);
        return $orders->values();
    }


    /**
     * 出荷枚数と在庫数のチェックを行う。
     *
     * @param int $stockId
     * @param int $pcsPerSet
     * @param integer $lot 個数
     * @param int $index 行番号
     * @throws ShipmentOrderException
     */
    private function checkShipment(int $stockId, int $pcsPerSet,  int $lot, int $index) {
            $shipment = $pcsPerSet == 0 ? $lot : $pcsPerSet * $lot;
            $errorKey = '';
            $stock = Stockpile::find($stockId);
            if ($stock->quantity == 0) {
                $errorKey = 'zero_quantity';
            } else if ($shipment > $stock->quantity) {
                $errorKey = 'excess-shipment';
            }
            if (!empty($errorKey)) {
                throw new ShipmentOrderException(Response::HTTP_BAD_REQUEST, $stockId, $errorKey, $index);
            }
    }

    protected function createRow(int $index, array $row) {
        return new ShiptRow($index, $row);
    }

    /**
     * 注文番号とCSVヘッダーのtypeに該当する行を取得する。
     *
     * @param string $orderId
     * @param Collection $rows
     * @param MercariCsvType $type
     * @return Collection
     */
    private function findRowByOrderId(string $orderId, Collection $rows, MercariCsvType $type):Collection {
        return $rows->where(SC::ORDER_ID, $orderId)->where(SC::TYPE, $type->value);
    }

    /**
     * 商品名からセット枚数を抽出する。
     *
     * @return int
     */
    public function extractPerSet(string $name):int {
        $perSet = 0;
        if (preg_match('/(\d+)枚\s*セット/u', $name, $matches)) {
            $perSet = (int) $matches[1];
        }
        return $perSet;
    }

    /**
     * 各商品の小計を算出する。
     *
     * @param array $item 商品情報
     * @param integer $feePrice
     * @param integer $itemCount
     * @param boolean $isFreeShipping
     * @return integer
     */
    private function calcItemSubtotal(array $item, int $feePrice, int $itemCount, bool $isFreeShipping):int {
        $productPrice = $item[SC::PRODUCT_PRICE];
        $quantity = $item[SC::QUANTITY];
        if ($isFreeShipping) {
            $productPrice -= round($feePrice / $itemCount);
        }

        return $productPrice * $quantity;
    }

    /**
     * 1枚あたりの単価を算出する。
     *
     * @param int $productPrice 商品行の"product_price"の値
     * @param bool $isSet セット販売フラグ
     * @param int $lot 個数
     * @param int $pcsPerSet セット枚数
     * @return int
     */
    private function calcUnitPrice(int $productPrice, bool $isSet, int $pcsPerSet):int {
        if ($isSet) {
            return round($productPrice / $pcsPerSet);
        }
        return $productPrice;
    }
}

