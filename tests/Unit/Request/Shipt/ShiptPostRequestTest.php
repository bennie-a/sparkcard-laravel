<?php
namespace Tests\Unit\Request\Shipt;
use App\Http\Requests\Shipt\ShiptPostRequest;
use App\Http\Requests\Shipt\ShiptStoreRequest;
use App\Services\Constant\GlobalConstant;
use Illuminate\Foundation\Http\FormRequest;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;
use Tests\Unit\Request\AbstractValidationTest;
use App\Services\Constant\ShiptConstant as SC;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * ShiptPostRequestクラスをテストするクラス
 */
#[CoversClass(ShiptPostRequest::class)]
class ShiptPostRequestTest extends AbstractValidationTest {

    #[Test]
    #[TestWith(['td'], '発送日が今日')]
    #[TestWith(['yd'], '発送日が昨日')]
    #[TestWith(['tmr'], '発送日が明日')]
    #[TestWith([''], '発送日が未入力')]
    #[TestDox('発送日に関する正常系テスト')]
    public function ok_shippingDate(string $dateKey): void {
        $date = ShiptLogTestHelper::getShiptDate($dateKey);
        $request = ShiptLogTestHelper::createStoreRequest();
        $request[SC::SHIPPING_DATE] = $date;
        $this->ok_pattern($request);
    }

    #[Test]
    #[TestWith([SC::ORDER_ID], '注文番号')]
    #[TestWith([SC::BUYER], '購入者名')]
    #[TestWith([SC::ZIPCODE], '郵便番号')]
    #[TestWith([SC::ADDRESS], '住所')]
    #[TestWith([SC::ITEMS], '商品情報')]
    #[TestDox('購入者情報の必須項目自体が未設定の場合のエラーチェック')]
    public function ng_buyer_info_key_lacked(string $key) {
        $request = ShiptLogTestHelper::createStoreRequest();
        unset($request[$key]);
        $attribute = ShiptLogTestHelper::attribute($key);
        $this->ng_pattern($request, [$key => $attribute.'は必ず入力してください。']);
    }

    #[Test]
    #[TestWith([GlobalConstant::ID], '在庫ID')]
    #[TestWith([SC::SHIPMENT], '出荷枚数')]
    #[TestWith([SC::SINGLE_PRICE], '1枚あたりの単価')]
    #[TestWith([SC::TOTAL_PRICE], '支払い金額')]
    #[TestWith([SC::IS_REGISTERED], '登録済みフラグ')]
    #[TestDox('商品情報の必須項目自体が未設定の場合のエラーチェック')]
    public function ng_item_info_key_lacked(string $key) {
        $request = ShiptLogTestHelper::createStoreRequest(2);
        unset($request[SC::ITEMS][1][$key]);
        $attribute = ShiptLogTestHelper::attribute($key);
        $item_key = SC::ITEMS .'.1.'. $key;
        $this->ng_pattern($request, [$item_key => $attribute.'は必ず入力してください。']);
    }

    #[Test]
    #[TestWith(['', '必ず入力してください。'], '未入力')]
    #[TestWith(['１２３４あ', '半角英数字と記号を入力してください。'], '全角数字')]
    #[TestDox('注文番号に関するエラーチェック')]
    public function ng_orderid(string $value, string $msg): void {
        $request = ShiptLogTestHelper::createStoreRequest();
        $request[SC::ORDER_ID] = $value;
        $this->ng_pattern($request, [SC::ORDER_ID => '注文番号は'.$msg]);
    }

    protected function createRequest(): FormRequest
    {
        return new ShiptPostRequest();
    }


}
