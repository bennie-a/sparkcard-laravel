<?php
namespace App\Services\Notion;

use App\Exceptions\api\Shipt\ShiptNotionException;
use App\Facades\CardBoard;

/**
 * Notionに関するサービスクラス
 */
class NotionService
{

    /**
     * 注文番号に紐づいたNotionカードが存在する
     * チェックする。
     *
     * @param string $orderId 注文番号
     * @throws ShiptNotionException
     */
    public function hasNotionCard(string $orderId) {
        $notionCard = CardBoard::findByOrderId($orderId);
        if ($notionCard->isEmpty()) {
            throw new ShiptNotionException();
        }
    }
}
