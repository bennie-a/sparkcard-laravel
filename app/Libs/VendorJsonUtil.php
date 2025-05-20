<?php
namespace App\Libs;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as ACon;
class VendorJsonUtil
{
    /**
     * ベンダー情報をJSON形式に変換する。
     *
     * @param array $array
     * @param string $vendor
     * @return array
     */
    public static function setVendorInfo(int $id, string $name, string $supplier): array
    {
        return [GCon::ID => $id, Header::NAME => $name, ACon::SUPPLIER => $supplier];
    }
}