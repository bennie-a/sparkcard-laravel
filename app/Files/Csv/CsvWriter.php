<?php
namespace App\Files\Csv;

/** 
 * CSVファイル出力クラス
 */
class CsvWriter {

    public function write(string $filename, array $header, $data, string $encode = "utf-8") {
        $dir = config('csv.export');
        $f = fopen($dir.$filename, 'w');
        if ($f) {
            $charset = sprintf("convert.iconv.utf-8/%s", $encode);
            stream_filter_prepend($f, $charset);
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
