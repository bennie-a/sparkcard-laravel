<?php
namespace Tests\Unit\Upload\Rel2024;

use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * MKMの特別版に関するテストクラス
 */
class MkmJsonFileTest extends AbstractCardJsonFileTest {
    public function promoProvider() {
        return [
            '「事件簿」ショーケース' => ['375', 'dossier'],
            '「拡大鏡」ショーケース' => ['315', 'magnified'],
            '大都市ラヴニカ' => ['318', 'ravnicacity']
     ];
    }

    public function excludeprovider() {
        return [
            '不可視インク仕様' => ['377'],
            'シリアル番号付き' => ['317z'],
        ];
    }
   
    protected function getSetCode():string
    {
        return 'MKM';
    }
}