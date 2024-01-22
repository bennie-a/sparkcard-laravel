<?php
namespace App\Files\Csv;

/** 
 * CSVファイル出力クラス
 */
class CsvWriter {

    public function write(string $filename, array $header, $data, string $encode = "UTF-8") {
        $dir = config('csv.export');
        $f = fopen($dir.$filename, 'w');
        if ($f) {
            stream_filter_prepend($f, $encode);
            // header
            if (!empty($header)) {
                fputcsv($f, $header);
            }

            // data
            foreach($data as $d) {
                fputcsv($f, $d);
            }
        }
        fclose($f);
    }
}    
