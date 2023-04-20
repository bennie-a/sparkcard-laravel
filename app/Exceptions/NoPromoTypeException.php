<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * カードマスタ登録：プロモタイプが該当しない時に発生する例外クラス
 */
class NoPromoTypeException extends HttpException
{
    protected $message = '';
    public function __construct(string $name, string $number)
  {
    $this->message = $name.'(カード番号:'.$number.')に該当するプロモタイプがありません';
  }
}
