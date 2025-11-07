<?php
namespace App\Files\Csv;

use App\Exceptions\api\CsvFormatException;
use App\Http\Response\CustomResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use League\Csv\Reader;
use League\Csv\Statement;

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
        // ファイル存在チェック
        if (!file_exists($path)) {
            $response = response()->json([
                'status' => 'File Not Found',
                'error' => 'ファイルが存在しません'
            ], Response::HTTP_BAD_REQUEST);
            throw new HttpResponseException($response);
        }

        // 文字コード検出
        $character_codes = ['ASCII', 'ISO-2022-JP', 'UTF-8', 'EUC-JP', 'SJIS'];
        $file_encoding = mb_detect_encoding(file_get_contents($path), $character_codes, true);
        logger()->debug("文字コード：$file_encoding");

        // league/csv 9.9対応: SplFileObject を使用
        $reader = Reader::createFromPath($path, 'r');

        // BOM除去 (もしUTF-8 BOM付きなら)
        $reader->setOutputBOM(Reader::BOM_UTF8);

        // 1行目をヘッダーとして取得
        $reader->setHeaderOffset(0);
        $fileHeaders = $reader->getHeader();

        // ヘッダー検証
        $exHeaders = $this->csvHeaders();
        $missingHeaders = array_diff($exHeaders, $fileHeaders);
        if (!empty($missingHeaders)) {
            $details = 'ヘッダーが足りません: ' . implode(', ', $missingHeaders);
            throw new CsvFormatException('ヘッダー不足', $details);
        }

        // 全レコードを取得
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        logger()->debug($reader->count() . "件のレコードを取得");
        $rows = [];
        foreach ($records as $record) {
            $convertedRow = [];
            foreach ($record as $key => $value) {
                $convertedRow[$key] = mb_convert_encoding($value, 'UTF-8', $file_encoding);
            }
            $rows[] = $convertedRow;
            logger()->debug($convertedRow);
        }

        // バリデーション処理
        $this->validate($rows);

        return $rows;
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