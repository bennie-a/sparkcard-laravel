<?php
namespace Tests\Unit\Upload\Rel2023;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * WOEのプロモタイプに関するテストクラス
 */
class WoeJsonFileTest extends AbstractCardJsonFileTest {
    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    public function promoProvider() {
        return [
            '単色の出来事付きカード' => ['4', 'draft'],
            '多色の出来事付きカード' => ['229', 'draft']
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
        return 'WOE';
    }
}