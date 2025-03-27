<?php
namespace Tests\Trait;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use App\Models\VendorType;
use App\Services\Constant\ArrivalConstant as Con;
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
    protected function verifyCard(array $json) {
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

        $act_foil = $json[Header::FOIL];
        $this->assertEquals($expected->isFoil, $act_foil['is_foil']);
        if ($expected->isFoil) {
            $exp_foil = Foiltype::find($expected->foiltype_id);
            $this->assertEquals($exp_foil->name, $act_foil[Header::NAME]);
        } else {
            $this->assertEmpty($act_foil[Header::NAME]);
        }
    }
    
    /**
     * vendor要素を検証する。
     *
     * @param string $vendor_type_id
     * @param array $vendor
     * @return void
     */
    public function verifyVendor(string $vendor_type_id, array $vendor) {
        $this->assertNotNull($vendor, 'vendor要素の有無');

        $type = VendorType::find($vendor_type_id);
        $this->assertEquals($vendor_type_id, $vendor[Con::ID], '取引先カテゴリID');
        $this->assertEquals($type->name, $vendor[Header::NAME], '取引先カテゴリ名');
        if ($vendor_type_id === '3') {
            $this->assertNotNull($vendor['supplier']);
        } else {
            $this->assertEmpty($vendor['supplier']);
        }
    }
}
