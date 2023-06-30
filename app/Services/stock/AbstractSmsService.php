<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Http\Response\CustomResponse;
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
        $validator = $this->getValidator();
        $errors = $validator->validateCsv($records);
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
                                    'skip' => count($this->ignore), 'error' => count($this->error), 'details' => $this->error];
        return $result;
    }

    public function validateCsv(array $records) {
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
        logger()->info('success', ['number' => $number]);
        $this->success[] = $number;
    }

    protected function addSkip(int $number, string $judge) {
        logger()->info('skip', [$number, $judge]);
        $this->ignore[] = $judge;
    }

    protected function addError(int $number, string $judge) {
        logger()->info('error', [$number , $judge]);
        $this->error[$number] = $judge;
    }

    protected abstract function getValidator();

    protected abstract function store(int $key, array $row);

    /**
     * 機能に応じたCsvReaderクラスを呼び出す。
     * @return CsvReader
     */
    protected abstract function csvReader();

    /**
     * CSVデータ1行分のバリデーションルールを取得する。
     * @deprecated
     * @return array
     */
    protected abstract function validationRules():array;

    /**
     * CSVデータのバリデーションチェックに使う項目名を取得する。
     *@deprecated 
     * @return array
     */
    protected abstract function attributes():array;

}