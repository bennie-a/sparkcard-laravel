<?php
namespace App\Exceptions\api;
use App\Exceptions\api\NotFoundException;

/**
 * プロモタイプが見つからない場合に発生する例外クラス
 */
class NoPromoTypeException extends NotFoundException
{
    private $number;

    private $promoValue;

    public function __construct($number, $promoValue)
    {
        $this->number = $number;
        $this->promoValue = $promoValue;
        parent::__construct();      
    }

    public function getTitle(): string
    {
        return 'プロモタイプなし';
    }

    public function getDetail(): string
    {
        return "指定したプロモタイプが見つかりません。number:{$this->number}, promotype:{$this->promoValue}";
    }

}