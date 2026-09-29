<?php
namespace App\Rules;

use App\Enum\ShopPlatform;
use Illuminate\Validation\Rule;

/**
 * 指定したプラットフォームが
 * 'mercari'か'base'か検証するクラス
 */
class PlatformRule
{

    public static function rule()
    {
        return Rule::in([ShopPlatform::BASE->value, ShopPlatform::MERCARI->value]);
    }
}
