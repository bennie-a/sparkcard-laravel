<?php
namespace App\Traits\Rules;
/**
 * 数量のバリデーションルール
 *
 * @package App\Traits\Rules
 */
trait QtyRules
{
    /**
     * 数量のバリデーションルール
     *
     * @return array
     */
    public static function qtyRules(): array
    {
        return ['integer', 'min:1'];
    }
}