<?php
namespace App\Rules;
use Illuminate\Validation\Rule;

/**
 * 日付フォーマットが'yyyy/nn/dd'であるバリデーションルールを定義するクラス
 */
class DateFormatRule {
     public static function slashRules() {
        return 'date_format:Y/m/d';
    }
}
