<?php
namespace App\Services\Shipt;

use App\Exceptions\api\Shipt\ShipmentOrderException;
use App\Files\Reader\ShiptLogCsvReader;
use App\Services\AbstractCsvService;

/**
 * 注文CSVの解析を行うサービスクラス
 */
class ShiptLogCsvService extends AbstractCsvService {

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
        $orders = [];
        foreach ($records as $index => $r) {
            $row = $this->createRow($index, $r);
            $orderId = $row->order_id();
            try {
                $this->hasNotionCard($orderId);
                if(!isset($orders[$orderId])){
                    // 新規生成
                    $orders[$orderId] = [
                        SC::ORDER_ID => $orderId,
                        GC::DATA => $row,
                    ];
                }
                // 出荷商品情報チェック
                $stockId = (int)$r[SC::PRODUCT_ID];
                $isRegistered = $this->repo->existsItem($orderId, $stockId);
                if (!$isRegistered) {
                    $this->checkShipment($stockId, $row->shipment());
                }
                $stock = Stockpile::find($stockId);

                $orders[$orderId][SC::ITEMS][] = [
                    StockpileHeader::STOCK => $stock,
                    SC::SHIPMENT => $row->shipment(),
                    SC::PRODUCT_PRICE => $row->product_price(),
                    SC::DISCOUNT_AMOUNT => $row->discount(),
                    SC::IS_REGISTERED => $isRegistered,
                ];
                } catch (ShipmentOrderException $e) {
                    $this->addError($row->number(), $e->getMsg());
                    continue;
                }
            }
        return array_values($orders);
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

}
