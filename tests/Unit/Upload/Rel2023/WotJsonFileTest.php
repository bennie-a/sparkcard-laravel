<?php
namespace Tests\Unit\Upload\Rel2023;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * WOTのプロモタイプに関するテストクラス
 */
class WotJsonFileTest extends AbstractCardJsonFileTest {
    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    public function promoProvider() {
        return [
            'おとぎ話' => ['1', 'adventure'],
            'アニメ・ボーダレス' => ['90', 'anime'],
        ];
    }

    /**
     * 特定の特別版が含まれないか検証するテストデータ
     *
     * @return void
     */
    public function excludeProvider() {
        return [];

    }
    
    protected function getSetCode():string
    {
        return 'WOT';
    }
}