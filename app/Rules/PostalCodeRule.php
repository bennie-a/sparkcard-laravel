<?php
namespace App\Rules;
use Illuminate\Validation\Rule;

/**
 * 郵便番号のバリデーションルールを定義するクラス
 */
class PostalCodeRule {
     public static function rules() {
        return 'regex:/^\d{3}-\d{4}$/';
    }
}
