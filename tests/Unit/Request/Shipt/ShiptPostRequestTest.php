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
    #[TestDox('購入者情報の必須項目が未設定の場合のエラーチェック')]
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
    #[TestDox('商品情報の必須項目が未設定の場合のエラーチェック')]
    public function ng_item_info_key_lacked(string $key) {
        $request = ShiptLogTestHelper::createStoreRequest();
        unset($request[SC::ITEMS][0][$key]);
        $this->ng_item_info($key, $request, 'は必ず入力してください。');
        }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['１２３４あ', 'は半角英数字と記号を入力してください。'], '全角数字')]
    #[TestDox('注文番号に関するエラーチェック')]
    public function ng_orderid(string $value, string $msg): void {
        $this->ng_buyer_info(SC::ORDER_ID, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith([' ', 'は必ず入力してください。'], '空白のみ')]
    #[TestDox('購入者名に関するエラーチェック')]
    public function ng_buyername(string $value, string $msg): void {
        $this->ng_buyer_info(SC::BUYER, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['1000001', 'の形式が異なります。'], 'ハイフンなし')]
    #[TestWith(['１２３-４５６７', 'の形式が異なります。'], '全角数字')]
    #[TestWith(['123-456a', 'の形式が異なります。'], '半角数字とハイフン以外の文字が含まれている')]
    #[TestDox('郵便番号に関するエラーチェック')]
    public function ng_zipcode(string $value, string $msg): void {
        $this->ng_buyer_info(SC::ZIPCODE, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestDox('住所に関するエラーチェック')]
    public function ng_address(string $value, string $msg): void {
        $this->ng_buyer_info(SC::ADDRESS, $value, $msg);
    }

    #[Test]
    #[TestDox('商品情報に空の配列が入った場合のエラーチェック')]
    public function ng_items_empty() {
        $request = ShiptLogTestHelper::createStoreRequest();
        $request[SC::ITEMS] = [];
        $this->ng_pattern($request, [SC::ITEMS => '商品情報は必ず入力してください。']);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['aa', 'は数字で入力してください。'], '数字以外の文字列')]
    #[TestWith(['0', 'は1以上の数字を入力してください。'], '0')]
    #[TestDox('商品情報の在庫IDに関するエラーチェック')]
    public function ng_items_id(string $value, string $msg): void {
        $this->ng_item_info_validation(GlobalConstant::ID, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['aa', 'は数字で入力してください。'], '数字以外の文字列')]
    #[TestWith(['0', 'は1以上の数字を入力してください。'], '0')]
    #[TestDox('商品情報の出荷枚数に関するエラーチェック')]
    public function ng_shipment(string $value, string $msg): void {
        $this->ng_item_info_validation(SC::SHIPMENT, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['aa', 'は数字で入力してください。'], '数字以外の文字列')]
    #[TestWith(['49', 'は50以上の数字を入力してください。'], '50未満の数字')]
    #[TestDox('商品情報の支払い金額に関するエラーチェック')]
    public function ng_total_price(string $value, string $msg): void {
        $this->ng_item_info_validation(SC::TOTAL_PRICE, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['aa', 'は数字で入力してください。'], '数字以外の文字列')]
    #[TestWith(['0', 'は1以上の数字を入力してください。'], '0')]
    #[TestDox('商品情報の1枚あたりの単価に関するエラーチェック')]
    public function ng_single_price(string $value, string $msg): void {
        $this->ng_item_info_validation(SC::SINGLE_PRICE, $value, $msg);
    }

    #[Test]
    #[TestWith(['', 'は必ず入力してください。'], '未入力')]
    #[TestWith(['aa', 'はtrueかfalseを入力してください。'], 'boolean以外の文字列')]
    #[TestDox('商品情報の登録済みフラグに関するエラーチェック')]
    public function ng_is_registered(string $value, string $msg): void {
        $this->ng_item_info_validation(SC::IS_REGISTERED, $value, $msg);
    }

    private function ng_item_info_validation(string $key, string $value, string $msg) {
        $request = ShiptLogTestHelper::createStoreRequest();
        $request[SC::ITEMS][0][$key] = $value;
        $this->ng_item_info($key, $request, $msg);
    }

    private function ng_buyer_info(string $key, string $value, string $msg): void {
            $request = ShiptLogTestHelper::createStoreRequest();
        $request[$key] = $value;
        $attr = ShiptLogTestHelper::attribute($key);
        $this->ng_pattern($request, [$key => $attr.$msg]);
    }

    private function ng_item_info(string $key, array $request, string $msg): void {
        $item_key = SC::ITEMS .'.0.'. $key;
        $attr = ShiptLogTestHelper::attribute($key);
        $this->ng_pattern($request, [$item_key => $attr.$msg]);
    }

    protected function createRequest(): FormRequest
    {
        return new ShiptPostRequest();
    }


}
