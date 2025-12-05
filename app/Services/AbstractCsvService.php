<?php
namespace App\Services;

use App\Files\CsvReader;
use App\Services\Constant\ErrorConstant as EC;

/**
 * CSVファイル読み込みに関する抽象クラス
 */
abstract class AbstractCsvService {

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
        $records = $this->read($path);
        // DB登録
        $callback = function($row) {
            $this->store($row);
        };
        $details = 
        $this->execute($records, $callback);
        $result = ["total_rows"=>count($records), 'successful_rows' => count($this->success), 
                            'failed_rows' => count($this->error), 'failed_details' => $this->error,
                            'skip_rows' => count($this->ignore), 'skip_details' => $this->ignore];
        return $result;
    }

    protected function read(string $path) {
        logger()->info('読み込み開始', [$path]);
        $reader = $this->csvReader();
        $records = $reader->read($path);
        return $records;
    }

    /**
     * CSVデータに対して処理を実行する。
     *
     * @param [type] $records
     * @param [type] $callback
     * @return void
     */
    protected function execute($records, $callback) {
        $results = [];
        foreach($records as $key => $row) {
            $rowobj = $this->createRow($key, $row);
            $number = $rowobj->number();
            logger()->info('Start', ['number' => $number]);
            $result = $callback($rowobj);
            if (!empty($result)) {
                $results[] = $result;
            }
        }
        return $results;
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
        $this->ignore[] = ["number" => $number, "msg" => $judge];
    }

    protected function addError(int $number, string $judge) {
        logger()->error('error', [$number , $judge]);
        $this->error[] = [EC::ROW => $number, EC::MSG => $judge];
    }

    public function getError() {
        return $this->error;
    }

    /**
     * ファイル内容にエラーが発生するか検証する。
     *
     * @return bool true:エラーあり、false:エラーなし
     */
    public function hasError():bool {
        return !empty($this->error);
    }

    protected abstract function store($row);

    /**
     * 機能に応じたCsvReaderクラスを呼び出す。
     * @return CsvReader
     */
    protected abstract function csvReader();

    /**
     * CSVファイル1行分のオブジェクトを作成する。 
     *
     * @param integer $index
     * @param array $row
     * @return object
     */
    protected abstract function createRow(int $index, array $row);

}