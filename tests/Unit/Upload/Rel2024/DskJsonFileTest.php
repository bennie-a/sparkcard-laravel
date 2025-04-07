<?php
namespace Tests\Unit\Upload\Rel2024;

use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * 『ダスクモーン：戦慄の館』のテストケース
 */
class DskJsonFileTest extends AbstractCardJsonFileTest {
    public function promoProvider() {
        return [
            '「二重露光」ショーケース' => ['352', 'doubleexposure'],
            '「超常」フレーム' => ['302',  'paranormal'],
            '「鏡の怪物」ボーダレス' => ['348','mirror'],
            'ボーダレスサイクル土地' => ['331', 'borderless'],
            'ボーダレス「部屋」カード' => ['335', 'borderless'],
            '通常版魁渡' => ['220', 'draft'],
            'ボーダレス版魁渡' => ['328', 'borderless'],
            '二重露光版魁渡' => ['354', 'doubleexposure'],
        ];
    }

    public function excludeprovider() {
        return [
            '拡張アート' => ['370'],
            'フラクチャー・Foil' => ['396'],
            'テクスチャー・Foil' => ['406']
        ];
    }
   
    protected function getSetCode():string
    {
        return 'DSK';
    }
}