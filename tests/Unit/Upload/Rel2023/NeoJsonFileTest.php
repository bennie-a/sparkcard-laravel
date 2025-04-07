<?php
namespace Tests\Unit\Upload\Rel2023;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * NEOのプロモタイプに関するテストクラス
 */
class NeoJsonFileTest extends AbstractCardJsonFileTest {
    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    public function promoProvider() {
        return [
            'ブースターファン' => ['433', 'boosterfun'],
            'ショーケース' => ['371', 'showcase'],
            'ネオンインク' => ['429','neonink'],
            'ファイレクシア語' => ['427', 'showcase'],
        ];
    }
    
    /**
     * 特定の特別版が含まれないか検証するテストデータ
     *
     * @return void
     */
    public function excludeProvider() {
        return [
            '拡張カード' => ['445']
        ];
    }
    
    protected function getSetCode():string
    {
        return 'NEO';
    }
}