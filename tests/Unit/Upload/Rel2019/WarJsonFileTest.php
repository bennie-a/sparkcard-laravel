<?php
namespace Tests\Unit\Upload\Rel2019;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * WARのプロモタイプに関するテストクラス
 */
class WarJsonFileTest extends AbstractCardJsonFileTest {
    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    public function promoProvider() {
        return [
            '日本限定カード' => ['150','jpwalker'],
        ];
    }
    
    /**
     * 特定の特別版が含まれないか検証するテストデータ
     *
     * @return void
     */
    public function excludeProvider() {
        $this->markTestSkipped("除外する特別版なし");
        return [
        ];
    }
    
    protected function getSetCode():string
    {
        return 'WAR';
    }
}