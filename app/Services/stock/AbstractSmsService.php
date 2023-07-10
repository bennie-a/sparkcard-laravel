<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Http\Response\CustomResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\Stock\StockpileRow;

use function Symfony\Component\Mailer\Command\execute;

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
        $records = $this->read($path);
        // DB登録
        $callback = function($row) {
            $this->store($row);
        };
        
        $this->execute($records, $callback);
        $result = ["row"=>count($records), 'success' => count($this->success), 
                                    'skip' => count($this->ignore), 'error' => count($this->error), 'details' => $this->error];
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
        $this->ignore[] = $judge;
    }

    protected function addError(int $number, string $judge) {
        logger()->info('error', [$number , $judge]);
        $this->error[$number] = $judge;
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