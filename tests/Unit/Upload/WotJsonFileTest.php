<?php

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

    public function test_出来事ソーサリーのB面が含まれない() {
        $result = $this->ok('WOE');
        $filterd = array_filter($result, function($a) {
            if ($a['en_name'] == 'Betroth the Beast') {
                return $a;
            }
    });

        $this->assertEmpty($filterd, '除外カードがある');
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