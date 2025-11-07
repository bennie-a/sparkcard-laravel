<?php
namespace App\Exceptions\api;

use App\Exceptions\ApiException;
use App\Http\Response\CustomResponse;

/**
 * CSVファイルにフォーマットエラーが
 * 発生した時の例外クラス
 */
class CsvFormatException extends ApiException {

    private string $title;
    public function __construct(string $title, string $details)
    {
      $this->title = $title;
      $this->message = $details;
    }

    /**
     * @override ApiException
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @override ApiException
     * @return 442
     */
    public function getStatusCode(): int
    {
        return CustomResponse::HTTP_CSV_VALIDATION;
    }

    public function getDetail(): string
    {
        return $this->message;
    }
}