<?php
namespace App\Services\Stock;

use App\Exceptions\CsvValidationException;
use App\Files\CsvReader;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

/**
 * 在庫管理機能(Stock Management Service:SMS)の抽象クラス
 */
abstract class AbstractSmsService {

    private $success = [];

    private $ignore = [];

    private $error = [];

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
        foreach($records as $key => $row) {
            $this->store($key, $row);
        }
        $result = ["row"=>count($records), 'success' => count($this->success), 
                                    'ignore' => count($this->ignore), 'error' => count($this->error), 'details' => $this->error];
        return $result;
    }

    private function validateCsv(array $records) {
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
     * 登録成功結果を設定する。
     *
     * @param integer $number 行数
     * @return void
     */
    protected function addSuccess(int $number) {
        $this->success[] = $number;
    }

    protected function addIgnore(array $judge) {
        $this->ignore[] = $judge;
    }

    protected function addError(array $judge) {
        $this->error[] = $judge;
    }

    protected abstract function store(int $key, array $row);

    /**
     * 機能に応じたCsvReaderクラスを呼び出す。
     * @return CsvReader
     */
    protected abstract function csvReader();

    /**
     * CSVデータ1行分のバリデーションルールを取得する。
     *
     * @return array
     */
    protected abstract function validationRules():array;

    /**
     * CSVデータのバリデーションチェックに使う項目名を取得する。
     *
     * @return array
     */
    protected abstract function attributes():array;

}