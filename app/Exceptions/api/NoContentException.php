<?php
namespace App\Exceptions\api;
use App\Exceptions\ApiException;
use Illuminate\Http\Response;

/**
 * 検索結果が存在しない場合に発生する例外クラス
 */
class NoContentException extends ApiException {

    public function getTitle(): string
    {
        return '情報なし';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getDetail(): string
    {
        return '指定した情報がありません。';
    }

}