<?php

namespace App\Exceptions\api\Shipt;

use App\Exceptions\ApiException;
use App\Models\Stockpile;
/**
 * 出荷手続きに関する例外クラス
 */
class ShipmentOrderException extends ApiException
{
    public function __construct(int $status, int $stockId, string $errorkey, int $index)
    {
        $this->status = $status;
        $this->stockId = $stockId;
        $this->errorKey = $errorkey;
        $this->index = $index;
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
        $detail = $this->getMsg();
        return "在庫ID[{$this->stockId}] : {$detail}";
    }

    public function getMsg() : string
    {
        $detail = __("validation.items.{$this->errorKey}.detail");
        return $detail;
    }

    /**
     * 行番号を取得する。
     *
     * @return integer
     */
    public function getIndex(): int
    {
        return $this->index + 2;
    }
}
