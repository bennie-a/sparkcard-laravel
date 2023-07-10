<?php
namespace App\Files;

use App\Http\Response\CustomResponse;
use App\Http\Validator\AbstractCsvValidator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use League\Csv\Reader;

/**
 * CSVファイル読み込みクラス
 */
abstract class CsvReader {

    /**
     * ファイル読み込み処理を行う。
     *
     * @param string $path
     * @return array
     */
    public function read(string $path)
    {
        // ファイルが存在するかチェック
        if (!file_exists($path)) {
            $response = response()->json([
                'status' => 'File Not Found',
                'error' => 'ファイルが存在しません。'
            ], Response::HTTP_BAD_REQUEST);
            throw new HttpResponseException($response);
        }

        // 文字コードがUTF-8であるかチェック
        $file_encoding = mb_detect_encoding(file_get_contents($path));
        if ($file_encoding !== 'UTF-8') {
            throw new \Exception('File encoding is not UTF-8.');
        }

        // CSVファイルを読み込む
        $csv = Reader::createFromPath($path);
        // 指定したヘッダーがファイルに存在するかチェック
        $fileHeaders = $csv->fetchOne();
        $exHeaders = $this->csvHeaders();
        // if (count($fileHeaders) !== count($exHeaders)) {
        //     throw new \Exception('Header is invalid.');
        // }

        $count = 0;
        foreach($fileHeaders as $h) {
            if(in_array($h, $exHeaders)) {
                $count++;
            }
        }
        if (count($exHeaders) !== $count) {
            throw new \Exception('Header is not included.');
        }
        // ヘッダーを除いて1行ずつ配列にする
        $csv->setHeaderOffset(0);
        $records = [];
        foreach ($csv as $row) {
            $records[] = $row;
        }
        $this->validate($records);
        return $records;
    }

    /** 
     * CSVファイルのヘッダーを指定する。
     * @return  array
     */
    protected abstract function csvHeaders();

    protected abstract function validator();

    /**
     * CSVファイルの入力チェックを行う。
     * 
     */
    private function validate(array $records) {
        // CSVデータの入力値チェック
        $errors = $this->validator()->validate($records);
        if (!empty($errors)) {
            $response = response()->json([
                'status' => 'validation error',
                'errors' => $errors
            ], CustomResponse::HTTP_CSV_VALIDATION);
            throw new HttpResponseException($response);
        }
    
    }
}