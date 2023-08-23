<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
/**
 * WisdomGuildServiceのFacadeクラス。
 * 処理内容はWisdomGuildServiceを参照。
 */
class WisdomGuild extends Facade {
 /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'WisdomGuild';
    }

}