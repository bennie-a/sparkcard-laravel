<?php
namespace App\Exceptions\api;
use App\Exceptions\api\NotFoundException;

/**
 * Foilタイプが見つからない場合に発生する例外クラス
 */
class NoFoilTypeException extends NotFoundException
{
    private $name;
    private $number;

    public function __construct($name, $number)
    {
        $this->name = $name;
        $this->number = $number;
        parent::__construct();      
    }

    public function getTitle(): string
    {
        return 'Foilタイプなし';
    }

    public function getDetail(): string
    {
        return $this->name.'('.$this->number.')のFoilタイプが見つかりません';
    }

}