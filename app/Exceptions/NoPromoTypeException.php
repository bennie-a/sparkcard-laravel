<?php

namespace App\Exceptions;

use Exception;
/**
 * カードマスタ登録：プロモタイプが該当しない時に発生する例外クラス
 */
class NoPromoTypeException extends Exception
{
    protected $message = '';
    public function __construct(string $name, string $number)
  {
    $message = [
      '次のカードに該当するプロモタイプがありません',
      "{ name => ".$name."}",
      "{ number => ".$number."}",
    ];

    $this->message = implode("\n", $message);
  }
}
