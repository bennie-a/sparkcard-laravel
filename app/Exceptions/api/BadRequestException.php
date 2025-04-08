<?php

namespace App\Exceptions\api;
use App\Exceptions\ApiException;
use Illuminate\Http\Response;

/**
 * 設定したエキスパンション略称が
 * 不正な場合に発生するExceptionクラス
 */
class BadRequestException extends ApiException {
    
    private $msgCode = '';
    public function __construct(string $msgCode)
    {
            $this->msgCode = $msgCode;
    }

    public function getTitle(): string
    {
        return '不正なリクエスト';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getDetail(): string
    {
        $message = __($this->msgCode);
        return $message;
   }
}