<?php
namespace Tests\Unit\Upload\Rel2024;

use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * MH3の特別版に関するテストクラス
 */
class Mh3JsonFileTest extends AbstractCardJsonFileTest {
    public function promoProvider() {
        return [
            '旧枠' => ['395', 'oldframe'],
            '「プロファイル」ボーダーレス' =>['363', 'profiles'],
            '「フレームブレイク」ボーダーレス' => ['343', 'flamebreak'],
            '「コンセプトアート」ボーダーレス版エルドラージ' => ['381', 'concept'],
        ];
    }

    public function excludeprovider() {
        return [];
    }
   
    protected function getSetCode():string
    {
        return 'MH3';
    }
}