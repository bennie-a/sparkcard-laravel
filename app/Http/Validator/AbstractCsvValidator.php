<?php
namespace App\Http\Validator;

use Illuminate\Support\Facades\Validator;
use App\Services\Constant\ErrorConstant as EC;

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
        $messages = array_merge($this->messages(), __('validation'));
        foreach($records as $key => $value) {
            $validator = Validator::make($value, $rules, $messages, $attributes);
            if ($validator->fails()) {
                $errors[] = [EC::ROW => $key + 2, EC::MSG  => $validator->errors()->first()];
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

    /**
     * 各機能固有のメッセージを取得する。
     *
     * @return array
     */
    public function messages():array {
        return [];
    }
}
