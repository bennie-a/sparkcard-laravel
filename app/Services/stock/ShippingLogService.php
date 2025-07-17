<?php
namespace App\Services\Stock;

use App\Exceptions\NotFoundException;
use App\Facades\CardBoard;
use App\Files\CsvReader;
use App\Files\Stock\ShippingLogCsvReader;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader as Header;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Date;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Entities\Properties\Select;
use FiveamCode\LaravelNotionApi\Entities\Properties\Text;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant;

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
     * @param ShippingRow $row
     * @return void
    */
    protected function store($row) {
        $notionCard = CardBoard::findByOrderId($row->order_id());
        if ($notionCard->isEmpty()) {
            $this->addError($row->number(), '該当するNotionカードがありません');
            return;
        }
        $card_id= $notionCard[0]->getProperty('sparkcard_id')->getContent();
        $stock = Stockpile::find($card_id, $row->setcode(), $row->condition(), $row->language(), $row->isFoil());
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

    
        $log = ['order_id' => $row->order_id(), Header::NAME => $row->buyer(), 'zip_code' => $row->postal_code(), 'address' => $row->address(),
                        'stock_id' => $stock['id'], Header::QUANTITY => $row->quantity(), 'shipping_date' => $row->shipping_date(),
                    'single_price' => $row->product_price(), 'total_price' => $row->total_price() ];
        ShippingLog::create($log);

        $stock->quantity = $stock->quantity - $row->quantity();
        if ($stock->quantity < 0) {
            $this->addError($row->number(), '在庫が足りません。');
            return;
        }

        $stock->update();
        $this->updateNotion($notionCard[0], $row);
        $this->addSuccess($row->number());
    }

    private function updateNotion(Page $notionCard, ShippingRow $row) {
        $page = new Page();
        $page->setId($notionCard->getId());
        $page->set('購入者名', Text::value($row->buyer()));
        $page->set('Status', Select::value('出荷準備中'));
        $page->set('発送日',  Date::value($row->shipping_date()));
        $page->set('sparkcard_id', Number::value(0));
        CardBoard::updatePage($page);

    }

    protected function createRow(int $index, array $row) {
        return new ShippingRow($index, $row);
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
                return ["id" => $slog["stock_id"],  Con::NAME => $slog["cardname"], Con::EXP => [Con::NAME => $slog[Header::SETNAME], Con::ATTR => $slog['exp_attr']],
                             Header::CONDITION => $slog[Header::CONDITION], Header::QUANTITY => $slog->quantity,Con::NUMBER => $slog[Con::NUMBER],
                            Header::LANG => $slog[Header::LANG], 'image_url' => $slog["image_url"], 
                            Header::FOIL => ['is_foil' => $slog['isFoil'], Con::NAME => $slog['foilname']],
                            'single_price' =>$slog->single_price, 'subtotal_price' => $slog->total_price,
                            Con::PROMOTYPE => [GlobalConstant::ID => $slog->promotype_id, GlobalConstant::NAME => $slog->promo_name
                ]];
        });
        // $items = array_map(function($log) {
        // }, $list);   
        $slog = $list[0];
        $info = [Header::ORDER_ID => $slog->order_id, Header::BUYER => $slog[Header::BUYER],
                        Header::SHIPPING_DATE => $slog->shipping_date,  'zipcode' => '〒'.$slog->zip, 
                        'address' => $slog->address, 'items' => $items->toArray()];
        return $info;
        // $log = ShippingLog::find($id);
    }

}