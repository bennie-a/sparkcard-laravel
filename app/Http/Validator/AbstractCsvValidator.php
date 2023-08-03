<?php
namespace App\Http\Validator;

use Illuminate\Support\Facades\Validator;

/**
 * CSV用のバリデーションクラス
 */
abstract class AbstractCsvValidator {

    /**
     * CSVの内容をバリエーションチェックする。
     *
     * @param array $records CSVファイルの内容
     * @return array
     */
    public function validate(array $records) {
        $rules = $this->validationRules();
        $attributes = $this->attributes();

        $errors = [];
        foreach($records as $key => $value) {
            $validator = Validator::make($value, $rules, __('validation'), $attributes);
            if ($validator->fails()) {
                $errors[] = [$key + 2 => $validator->errors()->all()];
            }
        }
        return $errors;
    }

    /**
     * バリデーションルールを設定する。
     */
    abstract protected function validationRules():array;

    /**
     * アトリビュートを設定する。
     *
     * @return array
     */
    abstract protected function attributes():array;


}