<?php
namespace App\Http\Response;

class CustomResponse {

    /** 定義されていない特別版が発生した時のステータスコード */
    const HTTP_NO_PROMOTYPE = 440;

    /** エキスパンションが見つからない時のステータスコード */
    const HTTP_NOT_FOUND_EXPANSION = 441;

    /**CSVデータにバリデーションエラーが発生した時のステータスコード */
    const HTTP_CSV_VALIDATION = 442;
}