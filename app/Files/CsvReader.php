<?php
namespace App\Files;
use League\Csv\Reader;

/**
 * CSVファイル読み込みクラス
 */
class CsvReader {
    private $headers;

    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    public function read(string $path)
    {
        // ファイルが存在するかチェック
        if (!file_exists($this->path)) {
            throw new \Exception('File not found.');
        }

        // 文字コードがUTF-8であるかチェック
        $file_encoding = mb_detect_encoding(file_get_contents($this->path));
        if ($file_encoding !== 'UTF-8') {
            throw new \Exception('File encoding is not UTF-8.');
        }

        // CSVファイルを読み込む
        $csv = Reader::createFromPath($this->path);

        // ヘッダーにsetcode,name,lang,condition,quantityが全てあるかチェック
        $fileHeaders = $csv->fetchOne();
        if (count($fileHeaders) !== count($this->headers)) {
            throw new \Exception('Header is invalid.');
        }

        foreach($fileHeaders as $h) {
            if(in_array($h, $this->header)) {
                echo "$h is included in csvHeaders";
            } else {
                echo "$h is not included in csvHeaders";
            }
        }

        // ヘッダーを除いて1行ずつ配列にする
        $records = [];
        foreach ($csv->setOffset(1) as $row) {
            $records[] = $row;
        }

        return $records;
    }
}