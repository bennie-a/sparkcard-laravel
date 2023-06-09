<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

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
