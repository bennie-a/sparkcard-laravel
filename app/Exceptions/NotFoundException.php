<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * 該当するデータが存在しない場合に発生する例外クラス
 */
class NotFoundException extends HttpException
{
    public function __construct(int $statusCode, string $message) {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

      /**
   * @version 2.2.0
   * @override HttpException
   * @return integer
   */
  public function getStatusCode(): int
  {
      return $this->statusCode;
  }

}
