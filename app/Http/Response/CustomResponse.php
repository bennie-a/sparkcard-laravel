<?php
namespace App\Http\Response;

class CustomResponse {

    /** エキスパンションが見つからない時のステータスコード */
    const HTTP_NOT_FOUND_EXPANSION = 441;

    /**CSVデータにバリデーションエラーが発生した時のステータスコード */
    const HTTP_CSV_VALIDATION = 442;

    /** カード情報がAPIに見つからなかった時のステータスコード */
    const HTTP_NOT_FOUND_CARD = 443;

    /** インポートに失敗したときのステータスコード */
    const HTTP_IMPORT_ERROR = 445;
}