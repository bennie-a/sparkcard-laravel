<?php
namespace App\Files\Csv;

/** 
 * CSVファイル出力クラス
 */
class CsvWriter {

    public function write(string $filename, array $header, $data) {
        $dir = config('csv.export');
        $f = fopen($dir.$filename, 'w');
        if ($f) {
            // header
            fputcsv($f, $header);

            // data
            foreach($data as $d) {
                fputcsv($f, $d);
            }
        }
        fclose($f);
    }
}    
