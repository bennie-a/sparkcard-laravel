<?php

namespace App\Exceptions;

use App\Http\Response\CustomResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * CSV形式のデータにバリデーションエラーが発生した時の例外クラス
 */
class CsvValidationException extends HttpException
{
    private $errorDetails = [];
    public function __construct(array $errorDetails)
  {
    $this->errorDetails = $errorDetails;
  }

  /**
   * @version 3.0.0
   * @override HttpException
   * @return integer
   */
  public function getStatusCode(): int
  {
      return CustomResponse::HTTP_CSV_VALIDATION;
  }

      /**
     * ログの出力
     * 
     * @return boolean|void trueかvoidならデフォルトのログを出力しない、falseなら出力する (reportableと逆)
     */
    public function report()
    {
        return false;
    }

    public function errorDetails() {
        return $this->errorDetails;
    }
}
