<?php
namespace Tests\Unit\Upload\Rel2025;

use Tests\Unit\Upload\AbstractCardJsonFileTest;
/**
 * TDMに関するテストクラス
 */
class TdmJsonFileTest extends AbstractCardJsonFileTest {
    
    public static function promoProvider() {
        return [
            'リバーシブル・ボーダーレス' => ['377', 'reversible_card'],
            '「氏族」ボーダーレス' =>['330', 'clan'],
            'ボーダーレス_包囲' =>['387', 'borderless'],
            'ボーダーレス_英雄譚' =>['388', 'borderless'],
            'ボーダーレス_エルズペス' =>['398', 'borderless'],
            '「ドラコニック」ショーケース_エンチャント' =>['297', 'dragonic'],
            '「ドラコニック」ショーケース_クリーチャータイプがドラゴン' => ['298', 'dragonic'],
            '「ドラコニック」ショーケース_クリーチャータイプが人間' => ['302', 'dragonic'],
        ];
    }

    public static function excludeprovider() {
        return [
            '「ドラゴンの眼」フルアート版土地(緑)' => ['291'],
            '「ドラゴンの眼」フルアート版土地(緑)' => ['291'],
            '幽霊火ショーケース' => ['400'],
            'ハロー・Foil仕様' => ['409'],
            'シリアル番号付き旧枠版' => ['419']
        ];
    }
    
    protected function getSetCode():string
    {
        return 'TDM';
    }
}