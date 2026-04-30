<?php

namespace App\Exceptions\api\Baseshop;

use App\Exceptions\ApiException;
use Exception;
use Illuminate\Http\Response;
use Throwable;

/**
 * BASE APIで接続にした場合のExceptionクラス
 */
class BaseApiException extends ApiException
{
    public function __construct(string $contents)
    {
        $json = json_decode($contents, true);
        $this->title = $json['error'];
        $this->message = $json['error_description'];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getDetail(): string
    {
        return $this->message;
    }
}
