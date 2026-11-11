<?php
namespace App\Services;

use App\Services\Constant\ErrorConstant as EC;
use App\Services\Shipt\ShiptRow;

/**
 * CSVファイル読み込みに関する抽象クラス
 */
abstract class AbstractCsvService {

    private $success = [];

    private $ignore = [];

    private $error = [];

    // /**
    //  * 注文CSVの内容を解析する。
    //  * @since 5.1.0
    //  * @param array $records CSV読み込み結果
    //  * @return array
    //  */
    // public function parse(array $records) {

    // }

    // protected abstract function parseRow(ShiptRow $row);

    /**
     * CSVデータに対して処理を実行する。
     *
     * @param array $records
     * @param callable $callback
     * @return void
     */
    protected function execute(array $records, callable $callback) {
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

    // public abstract function store(ShiptRow $row);

    /**
     * CSVファイル1行分のオブジェクトを作成する。
     *
     * @param integer $index
     * @param array $row
     * @return object
     */
    protected abstract function createRow(int $index, array $row);

}
