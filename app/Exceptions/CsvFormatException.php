<?php
namespace App\Exceptions;

use App\Http\Response\CustomResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * CSVファイルにフォーマットエラーが
 * 発生した時の例外クラス
 */
class CsvFormatException extends HttpException {
    public function __construct(string $details)
    {
      $this->message = $details;
    }

  /**
   * @version 2.2.0
   * @override HttpException
   * @return 445
   */
  public function getStatusCode(): int
  {
      return CustomResponse::HTTP_CSV_VALIDATION;
  }


}