<?php
namespace app\Services\Stock;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Carbon;

/**
 * 入荷ログの入力値オブジェクト
 */
class ArrivalParams {

    public function __construct(array $details) {
        $this->details = $details;
    }

    public function cardId():int {
        return (int)$this->details['card_id'];
    }

    public function condition():string {
        return $this->details[Header::CONDITION];
    }

    public function language():string {
        return $this->details['language'];
    }

    public function quantity():int {
        return intval($this->details[Header::QUANTITY]);
    }

    public function arrivalDate():Carbon {
        return $this->details[Header::ARRIVAL_DATE];
    }

    public function supplier():string {
        return $this->details[Header::SUPPLIER];
    }

    public function cost():int {
        return $this->details[Header::COST];
    }
}