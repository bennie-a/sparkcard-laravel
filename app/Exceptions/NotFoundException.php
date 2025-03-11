<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * 該当するデータが存在しない場合に発生する例外クラス
 * @deprecated 5.0.0
 */
class NotFoundException extends HttpException
{
    protected $statusCode;
    // public function __construct(int $statusCode, string $message) {
    //     $this->statusCode = $statusCode;
    //     $this->message = $message;
    // }

      /**
   * @version 2.2.0
   * @override HttpException
   * @return integer
   */
  public function getStatusCode(): int
  {
      return Response::HTTP_NO_CONTENT;
  }

}
