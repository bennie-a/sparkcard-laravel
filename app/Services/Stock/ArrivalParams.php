<?php
namespace app\Services\Stock;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\ArrivalConstant as Con;
use Illuminate\Support\Carbon;

/**
 * 入荷ログの入力値オブジェクト
 */
class ArrivalParams {

    private $details = [];
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
        return $this->details[Con::ARRIVAL_DATE];
    }
 
    public function vendorType():int {
        return $this->details[SCon::VENDOR_TYPE_ID];
    }

    public function vendor():string {
        if ($this->vendorType() !== 3) {
            return '';
        }
        return $this->details[Con::VENDOR];
    }
    
    public function cost():int {
        return $this->details[Header::COST];
    }

    public function details() : array {
        return $this->details;
    }
}