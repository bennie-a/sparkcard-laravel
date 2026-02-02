<?php

namespace App\Exceptions\api\Shipt;

use App\Exceptions\ApiException;
use App\Models\Stockpile;
/**
 * 出荷手続きに関する例外クラス
 */
class ShipmentOrderException extends ApiException
{
    public function __construct(int $status, int $stockId, string $errorkey)
    {
        $this->status = $status;
        $this->stockId = $stockId;
        $this->errorKey = $errorkey;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return  __("validation.items.{$this->errorkey}.title");
    }

    public function getDetail(): string
    {
        $detail = $this->getMsg($this->errorkey);
        return "在庫ID[{$this->stockId}] : {$detail}";
    }

    public function getMsg() : string
    {
        $detail = __("validation.items.{$this->errorKey}.detail");
        return $detail;
    }
}
