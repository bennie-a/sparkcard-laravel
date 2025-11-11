<?php
namespace App\Services\Shipt;

use App\Exceptions\NotFoundException;
use App\Facades\CardBoard;
use App\Files\CsvReader;
use App\Files\Reader\ShiptLogCsvReader;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\AbstractCsvService;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Date;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Entities\Properties\Select;
use FiveamCode\LaravelNotionApi\Entities\Properties\Text;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant;
use League\Csv\AbstractCsv;
use App\Services\Constant\ShiptConstant as SC;

/**
 * 出荷ログ機能のサービスクラス
 */
class ShiptLogService extends AbstractCsvService {
    /**
     * 出荷ログ用のCSV読み込みクラスを取得する。
     * @see CsvReader::csvReader
     * @return ShiptLogCsvReader
     */
    protected function csvReader() {
        return new ShiptLogCsvReader;
    }
    
    /**
     * @see AbstractSmsService::store
     * @param ShiptRow $row
     * @return void
    */
    protected function store($row) {
        $stock = Stockpile::findByShiptCsv($row);
        if (empty($stock)) {
            $this->addError($row->number(), '在庫データがありません');
            return;
        }
        // 出荷ログから注文IDと氏名、在庫IDを検索。
        // ➞あればエラー
        $isExists = ShippingLog::isExists($row->order_id(), $row->buyer(), $stock->id);
        if ($isExists) {
            $this->addSkip($row->number(), '既に登録されています');
            return;
        }

        if ($stock->quantity == 0) {
            $this->addError($row->number(), '在庫が0枚です。');
            return;
        }
    
        $log = ['order_id' => $row->order_id(), SC::NAME => $row->buyer(), 'zip_code' => $row->postal_code(), 'address' => $row->address(),
                        'stock_id' => $stock['id'], SC::QUANTITY => $row->quantity(), 'shipping_date' => $row->shipping_date(),
                    'single_price' => $row->product_price(), 'total_price' => $row->total_price() ];
        ShippingLog::create($log);

        $stock->quantity = $stock->quantity - $row->quantity();
        if ($stock->quantity < 0) {
            $this->addError($row->number(), '在庫が足りません。');
            return;
        }

        $stock->update();

        $notionCard = CardBoard::findByOrderId($row->order_id());
        if ($notionCard->isEmpty()) {
            $this->addError($row->number(), '該当するNotionカードがありません');
            return;
        }

        $this->updateNotion($notionCard[0], $row);
        $this->addSuccess($row->number());
    }

    /**
     * 注文CSVの内容を解析する。
     * @since 5.1.0
     * @param string $path
     * @return void
     */
    public function parse(string $path) {
        $records = $this->read($path);
        $orders = [];
        foreach ($records as $index => $r) {
            $row = $this->createRow($index + 2, $r);
            $orderId = $row->order_id();

            if(!isset($orders[$orderId])){
                // 新規生成
                $orders[$orderId] = [
                    SC::ORDER_ID => $orderId,
                    SC::BUYER => $row->buyer(),
                    SC::SHIPPING_DATE => $row->shipping_date(),
                    SC::ZIPCODE => $row->postal_code(),
                    SC::ADDRESS => $row->address(),
                    SC::ITEMS => [],
                ];
            }
        }
        return $orders;
    }

//     // subtotal_price
//     $subtotal = (int)$row['product_price'] * (int)$row['quantity'];

//     // single_price = subtotal / shipment（小数点切り捨て）
//     $single = (int) floor($subtotal / (int)$row['quantity']);

//     // item push
//     $orders[$orderId]['items'][] = [
//         'stock' => [
//             'id' => (int)$row['original_product_id'],
//         ],
//         'shipment' => (int)$row['quantity'],
//         'single_price' => $single,
//         'subtotal_price' => $subtotal,
//     ];
// }

// $json = json_encode(array_values($orders), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
// echo $json;
//         }

//         return $records;

    private function updateNotion(Page $notionCard, ShiptRow $row) {
        $page = new Page();
        $page->setId($notionCard->getId());
        $page->set('購入者名', Text::value($row->buyer()));
        $page->set('Status', Select::value('出荷準備中'));
        $page->set('発送日',  Date::value($row->shipping_date()));
        $page->set('sparkcard_id', Number::value(0));
        CardBoard::updatePage($page);

    }

    protected function createRow(int $index, array $row) {
        return new ShiptRow($index, $row);
    }

    public function fetch(array $details) {
        return ShippingLog::fetch($details);
    }

    /**
     * 出荷IDに該当する出荷情報を取得する。
     *
     * @param string $orderId
     * @return array
     */
    public function show(string $orderId) {
        $list = ShippingLog::fetchByOrderId($orderId);
        $items = $list->map(function($slog) {
                return ["id" => $slog["stock_id"],  Con::NAME => $slog["cardname"], Con::EXP => [Con::NAME => $slog[SC::SETNAME], Con::ATTR => $slog['exp_attr']],
                             SC::CONDITION => $slog[SC::CONDITION], SC::QUANTITY => $slog->quantity,Con::NUMBER => $slog[Con::NUMBER],
                            SC::LANG => $slog[SC::LANG], Con::IMAGE_URL => $slog[Con::IMAGE_URL], 
                            SC::FOIL => ['is_foil' => $slog['isFoil'], Con::NAME => $slog['foilname']],
                            'single_price' =>$slog->single_price, 'subtotal_price' => $slog->total_price,
                            Con::PROMOTYPE => [GlobalConstant::ID => $slog->promotype_id, GlobalConstant::NAME => $slog->promo_name
                ]];
        });
        // $items = array_map(function($log) {
        // }, $list);   
        $slog = $list[0];
        $info = [SC::ORDER_ID => $slog->order_id, SC::BUYER => $slog[SC::BUYER],
                        SC::SHIPPING_DATE => $slog->shipping_date,  SC::ZIPCODE => '〒'.$slog->zip, 
                        SC::ADDRESS => $slog->address, GlobalConstant::CARD => $items->toArray()];
        return $info;
        // $log = ShippingLog::find($id);
    }

}