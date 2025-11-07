<?php

namespace App\Models;

use App\Enum\CsvFlowType;
use App\Enum\ShopPlatform;
use App\Services\Constant\GlobalConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvHeader extends Model
{
    use HasFactory;

    protected $table = 'csv_header';
    
    const SHOP = 'shop';

    const COLUMN = 'column';

    const CSV_TYPE = 'csv_type';

    const ORDER_ID = 'order_id';

    protected $fillable = [GlobalConstant::ID, self::SHOP,  self::COLUMN, self::CSV_TYPE, self::ORDER_ID];

    /**
     * 'shop'カラムと'csv_type'が合致するレコードを取得する。
     *
     * @param ShopPlatform $shop
     * @param CsvFlowType $csvType
     * @return array
     */
    public static function findColumns(ShopPlatform $shop, CsvFlowType $csvType): array
    {
        $conditions = [
            [self::SHOP, '=', $shop->value],
            [self::CSV_TYPE, '=', $csvType->value],
        ];
       $result = self::select(self::COLUMN)->where($conditions)->orderBy(self::ORDER_ID)->get();
       return $result->pluck(self::COLUMN)->toArray();
    }
}