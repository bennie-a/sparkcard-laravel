<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Files\Stock\ShippingLogCsvReader;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader as Header;

/**
 * 出荷ログ機能のサービスクラス
 */
class ShippingLogService extends AbstractSmsService{

    /**
     * 出荷ログ用のCSV読み込みクラスを取得する。
     * @see CsvReader::csvReader
     * @return ShippingLogCsvReader
     */
    protected function csvReader() {
        return new ShippingLogCsvReader();
    }

        /**
     * @see AbstractSmsService::store
     * @param StockpileRow $row
     * @return void
     */
    protected function store($row) {
        $stock = Stockpile::find($row->card_name(), $row->condition(), $row->language(), $row->isFoil());
        if (!empty($stock)) {
            if ($stock->quantity == 0) {
                $this->addError($row->number(), '在庫が0枚です');
                return;
            }
            $log = ['order_id' => $row->order_id(), Header::NAME => $row->buyer(), 'zip_code' => $row->postal_code(), 'address' => $row->address(),
                         'stock_id' => $stock['id'], Header::QUANTITY => $row->quantity(), 'shipping_date' => $row->shipping_date(),
                        'single_price' => $row->product_price(), 'total_price' => $row->total_price() ];
            ShippingLog::create($log);

            $stock->quantity = $stock->quantity - $row->quantity();
            $stock->update();
            $this->addSuccess($row->number());
        } else {
            $this->addError($row->number(), '在庫情報なし');
        }
    }

    protected function createRow(int $index, array $row) {
        return new ShippingRow($index, $row);
    }


}