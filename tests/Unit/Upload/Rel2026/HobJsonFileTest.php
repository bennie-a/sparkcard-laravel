<?php
namespace Tests\Unit\Upload\Rel2026;

use Tests\Unit\Upload\AbstractCardJsonFileTest;
/**
 * HOBに関するテストクラス
 */
class HobJsonFileTest extends AbstractCardJsonFileTest {

    public static function promoProvider() {
        return [
            '通常版' => ['12', 'draft'],
            '計略カード' => ['205', 'hob_strategy'],
            'ブックカバー・カード' =>['241', 'book_cover'],
            '「竜の財宝」フレーム' =>['234', 'dragons_hoard'],
        ];
    }

    public static function excludeprovider() {
        return [
            'サージ・フォイル' => ['254'],
        ];
    }

    protected function getSetCode():string
    {
        return 'HOB';
    }
}
