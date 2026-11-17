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
     * @param int $index 行番号
     * @throws ShiptNotionException
     */
    public function hasNotionCard(string $orderId, int $index) {
        $notionCard = CardBoard::findByOrderId($orderId);
        if ($notionCard->isEmpty()) {
            throw new ShiptNotionException($index);
        }
    }
}
