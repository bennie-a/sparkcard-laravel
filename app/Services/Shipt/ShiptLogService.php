<?php
namespace App\Services\Shipt;

use App\Exceptions\api\NoContentException;
use App\Exceptions\api\NotFoundException;
use App\Facades\CardBoard;
use App\Libs\CarbonFormatUtil;
use App\Models\Shipt\Orders;
use App\Repositories\Api\Shipt\ShiptLogRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Date;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Entities\Properties\Select;
use FiveamCode\LaravelNotionApi\Entities\Properties\Text;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Notion\NotionService;
use DateTime;
use Illuminate\Database\Eloquent\Collection;

/**
 * 注文情報に関するサービスクラス
 */
class ShiptLogService {

    public function __construct(private ShiptLogRepository $repo) {

    }

    /**
     * 注文情報を1件登録する。
     * @param ShiptStoreRow $row
     * @return array
    */
    public function store(ShiptStoreRow $row):array {
        $orderId = $row->order_id();
        // Notionカードの存在チェック
        $nService = new NotionService();
        $nService->hasNotionCard($orderId);

        $order = $this->repo->findByOrderId($row->platform(), $orderId);
        if (!$order) {
            $order = $this->repo->createOrder($row);
        } else {
            $this->repo->updateOrder($order, $row);
        }

        // order_itemテーブルの登録
        $items = $row->items();
        foreach ($items as $item) {
            $stockId = (int)$item[GC::ID];
            // DB登録済みフラグがtrueの場合はスキップ
            if ($item[SC::IS_REGISTERED]) {
                logger()->warning("既に登録されています。注文ID:{$row->order_id()}, 氏名:{$row->buyer()}, 在庫ID:{$stockId}");
                continue;
            }

            $this->repo->createOrderItem($order->id, $item);
        }

        $notionCard = CardBoard::findByOrderId($row->order_id());
        $this->updateNotion($notionCard[0], $row);

        $lastLog = $this->repo->fetchLatestLog();
        return [GC::ID => $lastLog->id, GC::CREATE_AT => CarbonFormatUtil::format($lastLog->created_at)];
    }

    private function updateNotion(Page $notionCard, ShiptRow $row) {
        $page = new Page();
        $page->setId($notionCard->getId());
        $page->set('購入者名', Text::value($row->buyer()));
        $page->set('Status', Select::value('出荷準備中'));
        $page->set('発送日',  Date::value(new DateTime($row->shipping_date())));
        $page->set('sparkcard_id', Number::value(0));
        CardBoard::updatePage($page);

    }

    /**
     * 指定した条件に合う注文情報を取得する。
     *
     * @param array $details
     * @return Collection
     */
    public function fetch(array $details):Collection
    {
        $result = $this->repo->fetch($details);
        if ($result->isEmpty()) {
            throw new NotFoundException();
        }
        return $result;
    }

    /**
     * 出荷IDに該当する出荷情報を取得する。
     *
     * @param int $id
     * @return Orders
     */
    public function show(int $id) {
        $order = $this->repo->find($id);
        if (empty($order)) {
            throw new NoContentException();
        }
        return $order;
    }
}
