<?php
namespace App\Services\Stock;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Stock\Strategy\NoSetCodeStrategy;
use App\Services\Stock\Strategy\DefaultRowStrategy;

/**
 * 在庫情報CSV1件分のクラス
 */
class StockpileRow {
    
    protected $number;
    protected $row;
    public function __construct(int $number, array $row)
    {
        $this->number = $number + 2;
        $this->row = $row;
    }

    public function setcode() {
        return $this->row[Header::SETCODE];
    }

    public function name() {
        return $this->row[Header::NAME];
    }

    public function en_name() {
        return $this->row[Header::EN_NAME];
    }

    public function language() {
        $lang = $this->row[Header::LANG];
        return !empty($lang) ? $lang : 'JP';
    }

    public function quantity() {
        return $this->row[Header::QUANTITY];
    }

    public function condition() : string {
        $condition = $this->row[Header::CONDITION];
        return !empty($condition) ? $condition  : 'NM';
    }

    public function isFoil():bool {
        $isFoil = $this->row[Header::IS_FOIL];
        return !empty($isFoil) ? filter_var($isFoil, FILTER_VALIDATE_BOOLEAN) : false;
    }

    public function number() {
        return $this->number;
    }

    public function strategy() {
        if (empty($this->setcode())) {
            return new NoSetCodeStrategy();
        }
        return new DefaultRowStrategy();

    }
}