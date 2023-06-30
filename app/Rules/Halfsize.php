<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 半角英数字と記号を判定するクラス
 */
class Halfsize implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }
        return \preg_match('/^[a-zA-Z0-9!-\/:-@ \[-`{-~]+$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.halfsize');
    }
}
