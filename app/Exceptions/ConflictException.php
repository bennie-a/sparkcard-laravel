<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * 重複が発生した場合に発生する例外クラス
 */
class ConflictException extends HttpException
{

    public function __construct(string $name) {
        $this->message = $name.'は既に登録されています。';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}
