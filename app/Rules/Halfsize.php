<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 半角英数字と記号を判定するクラス
 */
class Halfsize implements ValidationRule
{

    /**
     * Run the validation rule.
     */
     // $attribute 属性名、$value 値、$fail 失敗時のメッセージ
     public function validate(string $attribute, mixed $value, Closure $fail): void
     {
        if (!empty($value) && \preg_match('/^[a-zA-Z0-9!-\/:-@ \[-`{-~]+$/', $value) == false) {
            $fail('validation.halfsize')->translate();
        }
     }
}
