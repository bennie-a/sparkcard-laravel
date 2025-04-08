<?php
namespace Tests\Unit\Upload\Rel2023;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

/**
 * ONEのプロモタイプに関するテストクラス
 */
class OneJsonFileTest extends AbstractCardJsonFileTest {
    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    public function promoProvider() {
        return [
            'ボーダレス「胆液」ショーケース' => ['345',  'oilslick'],
            'コンセプトアート' => ['416', 'concept'],
            'ステップアンドコンプリート' => ['422', 'stepandcompleat'],
        ];
    }

    /**
     * 特定の特別版が含まれないか検証するテストデータ
     *
     * @return void
     */
    public function excludeProvider() {
        return [
            'テクスチャーFoil' => ["573"],
            'コンフェッティFoil' => ["90"]
        ];
    }
    
    protected function getSetCode():string
    {
        return 'ONE';
    }
}