<?php
namespace App\Files;

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
        if (count($fileHeaders) !== count($exHeaders)) {
            throw new \Exception('Header is invalid.');
        }

        foreach($fileHeaders as $h) {
            if(!in_array($h, $exHeaders)) {
                throw new \Exception('Header is not included.');
            }
        }

        // ヘッダーを除いて1行ずつ配列にする
        $csv->setHeaderOffset(0);
        $records = [];
        foreach ($csv as $row) {
            $records[] = $row;
        }

        return $records;
    }

    /** 
     * CSVファイルのヘッダーを指定する。
     * @return  array
     */
    protected abstract function csvHeaders();
}