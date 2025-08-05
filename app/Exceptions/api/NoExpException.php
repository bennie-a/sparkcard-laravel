<?php
namespace App\Exceptions\api;
use App\Exceptions\api\NotFoundException;

/**
 * エキスパンションが見つからない時の例外クラス
 */
class NoExpException extends NotFoundException
{
    private $setCode;

    public function __construct($setCode)
    {
        $this->setCode = $setCode;
        parent::__construct();
    }

    public function getTitle(): string
    {
        return 'エキスパンションなし';
    }

    public function getDetail(): string
    {        
        return "指定したエキスパンションが見つかりません: {$this->setCode}";
    }
}