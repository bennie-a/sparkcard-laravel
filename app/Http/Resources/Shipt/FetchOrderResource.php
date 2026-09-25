<?php
namespace App\Http\Resources\Shipt;
use App\Http\Resources\Shipt\OrderResource;
use Override;

/**
 * 注文情報の検索結果を整形するResourceクラス
 * @since 6.1.0
 */
class FetchOrderResource extends OrderResource
{
    #[Override]
    protected function prevId():int
    {
        return -1;
    }

    #[Override]
    protected function nextId():int
    {
        return -1;
    }

    #[Override]
    public function hasItems():bool
    {
        return false;
    }
}
