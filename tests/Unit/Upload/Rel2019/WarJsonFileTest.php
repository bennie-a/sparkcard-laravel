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
    public static function promoProvider() {
        return [
            '日本限定カード' => ['150','jpwalker'],
        ];
    }
    
    /**
     * 特定の特別版が含まれないか検証するテストデータ
     *
     * @return void
     */
    public static function excludeProvider() {
        return [
        ];
    }
    
    protected function getSetCode():string
    {
        return 'WAR';
    }
}