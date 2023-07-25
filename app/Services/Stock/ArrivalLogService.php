<?php
namespace App\Services\Stock;

use App\Models\Stockpile;
use app\Services\Stock\ArrivalParams;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Services\Constant\StockpileHeader as Header;
use App\Http\Response\CustomResponse;
use App\Exceptions\NotFoundException;

/**
 * 入荷手続きに関するServiceクラス
 */
class ArrivalLogService {

    /**
     * 入荷ログを1件登録する。
     *
     * @param array $details
     * @return void
     */
    public function store(ArrivalParams $params) {
        $info = CardInfo::find($params->cardId());
        if (empty($info)) {
            throw new NotFoundException(CustomResponse::HTTP_NOT_FOUND_CARD, 'カード情報がありません');
        }

        $stockpile = Stockpile::findSpecificCard($params->cardId(), $params->language(), $params->condition());
        if (empty($stockpile)) {
            // 在庫情報なし
            $stockpile = Stockpile::create(['card_id' => $params->cardId(), 'language' => $params->language(),
            Header::CONDITION => $params->condition(), Header::QUANTITY => $params->quantity()]);
        } else {
            //在庫情報あり
            $stockpile->quantity += $params->quantity();
            $stockpile->update();
        }
        ArrivalLog::create(['stock_id' => $stockpile->id, 'arrival_date' => $params->arrivalDate(), 
                                        'supplier' => $params->supplier(), 'quantity' => $params->quantity(), 'cost' => $params->cost()]);
        \CardBoard::store($info, $params->details());
    }
}