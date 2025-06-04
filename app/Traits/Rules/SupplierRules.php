<?php
namespace App\Traits\Rules;
/**
 * 取引先のバリデーションルール
 *
 * @package App\Traits\Rules
 */
trait SupplierRules
{
    /**
     * 取引カテゴリIDのバリデーションルール
     *
     * @return array
     */
    public static function supplierRules(): array
    {
        return ['nullable', 'string', 'required_if:vendor_type_id,3'];
    }
}
