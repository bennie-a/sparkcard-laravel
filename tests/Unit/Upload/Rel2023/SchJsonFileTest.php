<?php
namespace Tests\Unit\Upload\Rel2023;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * SCHのプロモタイプに関するテストクラス
 */
class SchJsonFileTest extends AbstractCardJsonFileTest {
    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    public function promoProvider() {
        return [
            'テキストレス・フルアート' => ['17', 'textless'],
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
        return 'SCH';
    }
}