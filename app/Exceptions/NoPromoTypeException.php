<?php

namespace App\Exceptions;

use App\Http\Response\CustomResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * カードマスタ登録：プロモタイプが該当しない時に発生する例外クラス
 */
class NoPromoTypeException extends HttpException
{
    protected $message = '';
    public function __construct(string $name, string $number, string $promotype)
  {
    $this->message = $name.'(カード番号:'.$number.')に該当するプロモタイプ('.$promotype .')がありません';
  }

  /**
   * @version 2.2.0
   * @override HttpException
   * @return integer
   */
  public function getStatusCode(): int
  {
      return CustomResponse::HTTP_NO_PROMOTYPE;
  }

      /**
     * ログの出力
     * 
     * @return boolean|void trueかvoidならデフォルトのログを出力しない、falseなら出力する (reportableと逆)
     */
    public function report()
    {
        logger()->error('Not Found Promotype');
        return false;
    }
}
