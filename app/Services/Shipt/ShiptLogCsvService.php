<?php
namespace App\Services\Shipt;

use App\Enum\Shipt\MercariCsvType;
use App\Enum\ShopPlatform;
use App\Exceptions\api\Shipt\ShipmentOrderException;
use App\Files\Reader\ShiptLogCsvReader;
use App\Services\AbstractCsvService;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Models\Stockpile;
use App\Repositories\Api\Shipt\ShiptLogRepository;
use App\Services\Constant\StockpileHeader;
use App\Services\Notion\NotionService;
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
                $nService->hasNotionCard($orderId);
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

                $items = $productRows->map(function ($item, $index) use ($orderId) {
                    $stockId = (int)$item[SC::PRODUCT_ID];
                    return [
                        SC::INDEX => $index,
                        StockpileHeader::STOCK => Stockpile::find($stockId),
                        SC::PRODUCT_NAME => $item[SC::PRODUCT_NAME],
                        SC::SHIPMENT => $this->shipment($item[SC::PRODUCT_NAME], $item[SC::QUANTITY]),
                        SC::IS_REGISTERED => $this->repo->existsItem($orderId, $stockId),
                        SC::PRODUCT_PRICE => $item[SC::PRODUCT_PRICE],
                    ];
                })->values();

                $info = collect([SC::INDEX => $index, SC::PLATFORM => ShopPlatform::determine($orderId),
                                                                     SC::ORDER_ID => $orderId, SC::BUYER => $orderRow[SC::BUYER],
                                                                     SC::ORDER_PRICE => $orderRow[SC::ORDER_PRICE],
                                                                     SC::SHIPPING_PRICE => $orderRow[SC::SHIPPING_PRICE],
                                                                     SC::DISCOUNT_AMOUNT => $orderRow[SC::DISCOUNT_AMOUNT]]);
                $info = $info->merge($address);
                $info->put(SC::ITEMS, $items);
                $orders->put($orderId, $info);
            } catch (ShipmentOrderException $e) {
                $this->addError($index, $e->getMsg());
                logger()->error('Notionカードの取得に失敗', ['orderId' => $orderId, 'error' => $e->getMsg()]);
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
        return $orders->values()->toArray();
    }


    /**
     * 出荷枚数と在庫数のチェックを行う。
     *
     * @param int $stockId
     * @param integer $shipment
     * @throws ShipmentOrderException
     */
    private function checkShipment(int $stockId, int $shipment) {
            $errorKey = '';
            $stock = Stockpile::find($stockId);
            if ($stock->quantity == 0) {
                $errorKey = 'zero_quantity';
            } else if ($shipment > $stock->quantity) {
                $errorKey = 'excess-shipment';
            }
            if (!empty($errorKey)) {
                throw new ShipmentOrderException(HttpResponse::HTTP_BAD_REQUEST, $stockId, $errorKey);
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
     * 注文枚数を取得する。
     *
     * @return int
     */
    public function shipment(string $name, int $quantity):int {
        $shipment = 1;
        if (preg_match('/(\d+)枚\s*セット/u', $name, $matches)) {
            $shipment = (int) $matches[1];
        }
        return $shipment * $quantity;
    }
}

