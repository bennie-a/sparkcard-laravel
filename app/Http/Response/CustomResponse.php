<?php
namespace App\Http\Response;

class CustomResponse {

    /** 定義されていない特別版が発生した時のステータスコード */
    const HTTP_NO_PROMOTYPE = 440;

    /** エキスパンションが見つからない時のステータスコード */
    const HTTP_NOT_FOUND_EXPANSION = 441;

    /**CSVデータにバリデーションエラーが発生した時のステータスコード */
    const HTTP_CSV_VALIDATION = 442;

    /** カード情報がAPIに見つからなかった時のステータスコード */
    const HTTP_NOT_FOUND_CARD = 443;

    const HTTP_NOT_FOUND_FOIL = 444;

    /** インポートに失敗したときのステータスコード */
    const HTTP_IMPORT_ERROR = 445;
}