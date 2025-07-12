<?php
namespace App\Exceptions\api;
use App\Exceptions\api\NotFoundException;

/**
 * プロモタイプが見つからない場合に発生する例外クラス
 */
class NoPromoTypeException extends NotFoundException
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
        parent::__construct();      
    }

    public function getTitle(): string
    {
        return 'プロモタイプなし';
    }

    public function getDetail(): string
    {
        return "指定したプロモタイプが見つかりません: {$this->number}";
    }

}