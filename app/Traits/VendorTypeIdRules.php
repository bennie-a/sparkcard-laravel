<?php
namespace App\Traits;

/**取引カテゴリIDのバリデーションルール */
trait VendorTypeIdRules
{
    public static function vendorTypeIdRules(): array
    {
        return ['required', 'integer', 'between:1,5'];
    }
}
