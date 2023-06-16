<?php
namespace App\Services\Stock;

use App\Exceptions\CsvValidationException;
use App\Files\CsvReader;
use App\Http\Response\CustomResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

/**
 * 在庫管理機能(Stock Management Service:SMS)の抽象クラス
 */
abstract class AbstractSmsService {

        /**
     * CSVファイルの内容をDBに登録する。
     *
     * @param string $path ファイルパス
     * @return void
     */
    public function import(string $path) {
        logger()->info('読み込み開始', [$path]);
        $reader = $this->csvReader();
        $records = $reader->read($path);
        $errors = $this->validateCsv($records);
        $result = ["row"=>count($records)];
        if (!empty($errors)) {
            $response = response()->json([
                'status' => 'validation error',
                'errors' => $errors
            ], CustomResponse::HTTP_CSV_VALIDATION);
            throw new HttpResponseException($response);
        }
        return $result;
    }

    private function validateCsv(array $records) {
        $rules = [
            'setcode' =>['required']
        ];
        $attributes = [
            'setcode' => 'セット略称'
        ];

        $messages = [
            'setcode.required' => ':attributeを入力してください。',

        ];

        $errors = [];
        foreach($records as $key => $value) {
            $validator = Validator::make($value, $rules, $messages, $attributes);
            if ($validator->fails()) {
                $errors[] = [$key + 1 => $validator->errors()->all()];
            }
        }
        return $errors;
    }

    /**
     * 機能に応じたCsvReaderクラスを呼び出す。
     * @return CsvReader
     */
    protected abstract function csvReader();

}