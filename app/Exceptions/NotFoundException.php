<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct(int $statusCode, string $message) {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }
}
