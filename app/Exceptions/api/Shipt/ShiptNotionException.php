<?php

namespace App\Exceptions\api\Shipt;

use Exception;
use Illuminate\Http\Response;

/**
 * Notionカードが存在しない場合の例外クラス
 */
class ShiptNotionException extends ShipmentOrderException
{
    public function __construct()
    {
        parent::__construct(Response::HTTP_NOT_FOUND, 0, 'no-notion');
    }
}
