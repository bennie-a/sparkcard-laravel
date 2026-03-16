<?php

use Illuminate\Http\Response;

/**
 * 外部APIとの連携に失敗した場合の例外クラス
 */
class ApiConnectException extends ApiException {

    public function getTitle(): string
    {
        return 'API接続エラー';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_GATEWAY;
    }

    public function getDetail(): string
    {
        return '外部APIへの接続に失敗しました。';
    }
}
