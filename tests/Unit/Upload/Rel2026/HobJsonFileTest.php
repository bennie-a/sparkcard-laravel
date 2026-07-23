<?php
namespace Tests\Unit\Upload\Rel2026;

use Tests\Unit\Upload\AbstractCardJsonFileTest;
/**
 * HOBに関するテストクラス
 */
class HobJsonFileTest extends AbstractCardJsonFileTest {

    public static function promoProvider() {
        return [
            '計略カード' => ['205', 'hob_strategy'],
            'ブックカバー・カード' =>['241', 'book_cover'],
            '「竜の財宝」フレーム' =>['234', 'dragons_hoard'],
        ];
    }

    public static function excludeprovider() {
        return [
            'サージ・フォイル' => ['254'],
            // '「ドラゴンの眼」フルアート版土地(緑)' => ['291'],
            // '幽霊火ショーケース' => ['400'],
            // 'ハロー・Foil仕様' => ['409'],
            // 'シリアル番号付き旧枠版' => ['419']
        ];
    }

    protected function getSetCode():string
    {
        return 'HOB';
    }
}
