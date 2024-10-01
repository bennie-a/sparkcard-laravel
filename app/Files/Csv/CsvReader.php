<?php
namespace App\Files\Csv;

use App\Exceptions\CsvFormatException;
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

        // 文字コードを取得する。
        $character_codes = ['ASCII', 'ISO-2022-JP', 'UTF-8', 'EUC-JP', 'SJIS'];
        $file_encoding = mb_detect_encoding(file_get_contents($path), $character_codes, true);
        logger()->debug("文字コード：$file_encoding");

        // CSVファイルを読み込む
        $csv = Reader::createFromPath($path);
        // 指定したヘッダーがファイルに存在するかチェック
        $fileHeaders = $csv->fetchOne();
        $exHeaders = $this->csvHeaders();

        $count = 0;
        foreach($fileHeaders as $h) {
            if(in_array($h, $exHeaders)) {
                $count++;
            }
        }
        if (count($exHeaders) !== $count) {
            throw new CsvFormatException('CSVファイルのヘッダーが足りません');
        }
        
        // ヘッダーを除いて1行ずつ配列にする
        $csv->setHeaderOffset(0);
        $records = [];
        foreach ($csv as $row) {
            $records[] = mb_convert_encoding($row, 'UTF-8', $file_encoding);
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