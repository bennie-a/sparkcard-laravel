<?php
namespace App\Repositories\Api\Shipt;

use App\Enum\ShiptMethod;
use App\Models\Shipping;

/**
 * 発送方法に関するRepositoryクラス
 */
class ShiptMethodRepository
{
    /**
     * 発送方法名と合致するレコードを取得する。
     *
     * @param ShiptMethod $method 配送方法名
     * @param bool $isDeleted 削除フラグ
     * @return Shipping
     */
    public function findByMethod(ShiptMethod $method, bool $isDeleted):Shipping
    {
        return Shipping::where($method->value)->where('deleted', $isDeleted)->sole();
    }

}
