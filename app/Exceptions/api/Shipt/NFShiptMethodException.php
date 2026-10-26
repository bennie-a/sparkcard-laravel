<?php
namespace App\Exceptions\api\Shipt;
use App\Exceptions\ApiException;
use Illuminate\Http\Response;

/**
 * 配送方法が見つからない場合の例外クラス
 */
class NFShiptMethodException extends ApiException
{
    public function __construct(string $orderNo)
    {
        $this->orderNo = $orderNo;
    }

    public function getTitle(): string
    {
        return '配送方法なし';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getDetail(): string
    {
        return "{$this->orderNo}:該当する配送方法が見つかりません。";
    }
}
