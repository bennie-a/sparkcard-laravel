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
        // CSVデータの入力値チェック
        $errors = $this->validateCsv($records);
        if (!empty($errors)) {
            $response = response()->json([
                'status' => 'validation error',
                'errors' => $errors
            ], CustomResponse::HTTP_CSV_VALIDATION);
            throw new HttpResponseException($response);
        }

        // DB登録
        $this->store($records);
        $result = ["row"=>count($records)];
        return $result;
    }

    private function validateCsv(array $records) {
        $rules = [
            'setcode' => 'required|alpha_num',
            'name' => 'required',
            'lang' => 'required|in:JP,EN,IT,CT,CS',
            'condition' => 'required|in:NM,NM-,EX+,EX,PLD',
            'quantity' => 'required|numeric',
        ];
        $attributes = [
            'setcode' => 'セット略称',
            'name' => '商品名',
            'lang' => '言語',
            'condition' => '保存状態',
            'quantity' => '数量'
        ];

        $errors = [];
        foreach($records as $key => $value) {
            $validator = Validator::make($value, $rules, __('validation'), $attributes);
            if ($validator->fails()) {
                $errors[] = [$key + 2 => $validator->errors()->all()];
            }
        }
        return $errors;
    }

    public function store($records) {

    }

    /**
     * 機能に応じたCsvReaderクラスを呼び出す。
     * @return CsvReader
     */
    protected abstract function csvReader();

}