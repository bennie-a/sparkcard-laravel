<?php

namespace App\Exceptions;

use App\Http\Response\CustomResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * カードマスタ登録：プロモタイプが該当しない時に発生する例外クラス
 * @deprecated 4.11.0
 */
class NoPromoTypeException extends ApiException
{
    protected $message = '';
    public function __construct(string $name, string $number, string $promotype)
  {
    $this->message = $name.'(カード番号:'.$number.')に該当するプロモタイプ('.$promotype .')がありません';
  }

  /**
   * @version 2.2.0
   * @override ApiException
   * @return integer
   */
  public function getStatusCode(): int
  {
      return Response::HTTP_NOT_FOUND;
  }

  
  public function getTitle(): string
  {
      return 'プロモタイプ未登録';
  }

  public function getDetail(): string
  {
    return $this->message;
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
