<?php
namespace Tests\Trait;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use App\Models\Stockpile;
use App\Models\VendorType;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;

/**
 * 'GET'メソッドのAPIに関する検証クラス
 */
trait GetApiAssertions 
{
    protected function assert_OK(array $condition) {
        $response = $this->execute($condition);
        $response->assertOk();
        return $response;
    }

    protected function assert_NG(array $condition, int $status, string $message) {
        $response = $this->execute($condition);
        $response->assertStatus($status);
        $data = $response->json();
        $this->assertEquals($status, $data['status'], 'HTTPステータスコード');
        $this->assertEquals($message, $data['detail'], 'メッセージ');
        $this->assertEquals($this->getEndPoint(), $data['request'], 'エンドポイント');
    }

    /**
     * メソッドを'GET'で指定してAPIを実行する。
     *
     * @param array $condition
     * @return Response
     */
    private function execute(array $condition) {
        $response = $this->json('GET', $this->getEndPoint(), $condition);
        return $response;
    }

    
    /**
     * カード情報について検証する。
     *
     * @param array $json
     * @return void
     */
    protected function verifyCard(string $stock_id, array $json) {
        $expected = CardInfo::find($json[Con::ID]);
        logger()->debug('カード名:'.$json[Con::NAME]);
        $this->assertEquals($expected->name, $json[Con::NAME], 'カード名');
        $this->assertEquals($expected->number, $json[Con::NUMBER], 'カード番号');
        $this->assertEquals($expected->image_url, $json['image'], '画像URL');
        $this->assertEquals(CardColor::tryFrom($expected->color_id)->text(), $json[Con::COLOR], '色');

        $exp_expansion = Expansion::findByNotionId($expected->exp_id);
        $act_expansion = $json[Con::EXP];
        $this->assertEquals($exp_expansion->attr, $act_expansion[Con::ATTR], 'セット略称');
        $this->assertEquals($exp_expansion->name, $act_expansion[Con::NAME], 'セット名');
    }
    
    /**
     * 取引先カテゴリIDが買取の場合を検証する。
     *
     * @return void
     */
    protected function verifyBuyVendor() {
        return function($condition, $json, $log) {
            $vendor = $json[ACon::VENDOR];
            $this->assertNotNull($vendor);
            $this->assertEquals(3, $vendor[Con::ID], '取引先カテゴリID');
            $type = VendorType::find(3);
            $this->assertEquals($type->name, $vendor[Header::NAME], '取引先カテゴリ名');
            $this->assertNotNull($vendor['supplier']);
        };
    }

    /**
     * 取引先カテゴリが「買取」以外の場合を検証する。
     *
     * @return void
     */
    protected function verifyOtherVendor() {
        return function($condition, $json, $log) {
            $vendor = $json[ACon::VENDOR];
            $this->assertNotNull($vendor);
            $vendor_type_id = $vendor[Con::ID];
            $this->assertNotNull($vendor_type_id, '取引先カテゴリID');
            $type = VendorType::find($vendor_type_id);
            $this->assertEquals($type->name, $vendor[Header::NAME], '取引先カテゴリ名');
            $this->assertNull($vendor['supplier']);
        };
    }


    /**
     * カード情報のFoil要素が通常版か検証するクロージャを取得する。
     *
     * @param array $json
     * @return callable
     */
    public function verifyNonFoil() {
        return function($condition, $json, $log) {
            $foils = $json[Con::CARD][Header::FOIL];
            $this->assertFalse($foils['is_foil']);
            $this->assertEmpty($foils[Con::NAME]);
        };
    }

    /**
     * カード情報のFoil要素がFoil版か検証するクロージャを取得する。
     *
     * @param array $json
     * @return callable
     */
    public function verifyFoil() {
        return function($condition, $json, $log) {
            $card = $json[Con::CARD];
            $foils = $card[Header::FOIL];
            $this->assertTrue($foils['is_foil']);

            $cardinfo = CardInfo::find($card[Con::ID]);
            $foiltype = Foiltype::find($cardinfo->foiltype_id);
            $this->assertEquals($foiltype->name, $foils[Con::NAME]);
        };
    }
    

}
