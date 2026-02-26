<?php

namespace Tests\Unit\Validator;

use App\Http\Validator\ShiptValidator;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\ErrorConstant as EC;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Util\TestDateUtil;

/**
 * 注文CSVのValidatorクラスのテスト
 */
#[CoversClass(ShiptValidator::class)]
class ShiptValidatorTest extends TestCase
{
    #[Test]
    #[TestDox('バリデーションチェックOK')]
    #[DataProvider('okProvider')]
    public function Ok(string $key, string $value): void
    {
        $record = $this->createBuyerInfo();
        if ($key) {
            $record[$key] = $value;
        }

        $error = $this->validate($record);
        $this->assertTrue(empty($error), 'バリデーションエラーの有無');
    }

    public static function okProvider(): array
    {
        return [
            '全て入力済み' => ['', ''],
            '発送日が未入力' => [SC::SHIPPING_DATE, ''],
            '住所2が未入力' => [SC::ADDRESS_2, ''],
            '市町村名が市名' => [SC::CITY, '○○市'],
            '市町村名が区名' => [SC::CITY, '○○区'],
            '市町村名が郡名' => [SC::CITY, '○○郡'],
            '市町村名が町名' => [SC::CITY, '○○町'],
            '市町村名が村名' => [SC::CITY, '○○村'],
        ];
    }

    #[Test]
    #[TestDox('注文番号チェック')]
    #[DataProvider('orderIdProvider')]
    public function ngOrderId(string $value, string $msg) {
        $this->verifyBuyerError(SC::ORDER_ID, $value, $msg);
    }

    public static function orderIdProvider(): array
    {
        return [
            '未入力' => ['', '注文番号は必ず入力してください。'],
            '全角文字列' => ['ａｂｃ１２３', '注文番号は半角英数字と記号を入力してください。'],
            '改行コード混在' => ["abc\n123", '注文番号は半角英数字と記号を入力してください。'],
        ];
    }

    #[Test]
    #[TestDox('購入者名チェック')]
    public function ngBuyerName() {
        $this->verifyBuyerError(SC::BUYER, '', '購入者名は必ず入力してください。');
    }

    #[Test]
    #[TestDox('発送日チェック')]
    #[DataProvider('shippingDateProvider')]
    public function ngShippingDate($value, $msg) {
        $this->verifyBuyerError(SC::SHIPPING_DATE, $value, $msg);
    }

    public static function shippingDateProvider(): array
{
    return [
        '日付ではない' => ['1111', '発送日はY/m/d形式の日付で入力してください。'],
        'Y/m/d形式ではない' => ['2025-12-18', '発送日はY/m/d形式の日付で入力してください。'],
    ];
}

    #[Test]
    #[TestDox('郵便番号チェック')]
    #[DataProvider('postalCodeProvider')]
    public function ngPostalCode(string $value, string $msg) {
        $this->verifyBuyerError(SC::POSTAL_CODE, $value, $msg);
    }

    public static function postalCodeProvider(): array
    {
        return [
            '未入力' => ['', '郵便番号は必ず入力してください。'],
            '郵便番号が文字列' => ['aa', '郵便番号は「123-4567」の形式で入力してください。'],
        ];
    }

    #[Test]
    #[TestDox('住所チェック')]
    #[DataProvider('addressProvider')]
    public function ngAddress(string $key, string $value, string $msg) {
        $this->verifyBuyerError($key, $value, $msg);
    }

    public static function addressProvider(): array
    {
        return [
            '都道府県が未入力' => [SC::STATE, '', '都道府県名は必ず入力してください。'],
            '都道府県が47都道府県以外' => [SC::STATE, 'アメリカ', '入力した都道府県名は不正です。'],
            '市区町村名が未入力' => [SC::CITY, '', '市区町村名は必ず入力してください。'],
            '市区町村名が市区町村以外の文字列' => [
                SC::CITY,
                'aaaa',
                '市区町村名は「市」「区」「町」「村」「郡」を最後につけてください。',
            ],
            'その他住所1が未入力' => [SC::ADDRESS_1, '', 'その他住所1は必ず入力してください。'],
        ];
    }

    #[Test]
    #[TestDox('商品コードチェック')]
    #[DataProvider('productIdProvider')]
    public function ngProductId(string $value, string $msg) {
        $this->verifyItemError(GlobalConstant::ID, $value, $msg);
    }

    public static function productIdProvider(): array
    {
        return [
            '未入力' => ['', '商品コードは必ず入力してください。'],
            '文字列' => ['aaa', '商品コードは半角数字を入力してください。'],
            '全角数字' => ['１２３', '商品コードは半角数字を入力してください。'],
            '0' => ['0', '商品コードは1以上の数字を入力してください。'],
        ];
    }

    #[Test]
    #[TestDox('商品名チェック')]
    public function ngProductName() {
        $this->verifyItemError(SC::PRODUCT_NAME, '', '商品名は必ず入力してください。');
    }

    #[Test]
    #[TestDox('出荷枚数チェック')]
    #[DataProvider('quantityProvider')]
    public function ngQuantity(string $value, string $msg) {
        $this->verifyItemError(SC::QUANTITY, $value, $msg);
    }

    public static function quantityProvider(): array
    {
        return [
            '未入力' => ['', '出荷枚数は必ず入力してください。'],
            '文字列' => ['1aaaa', '出荷枚数は半角数字を入力してください。'],
            '全角数字' => ['１２３', '出荷枚数は半角数字を入力してください。'],
            '0' => ['0', '出荷枚数は1以上の数字を入力してください。'],
        ];
    }

    #[Test]
    #[TestDox('商品価格チェック')]
    #[DataProvider('productPriceProvider')]
    public function ngProductPrice(string $value, string $msg) {
        $this->verifyItemError(SC::PRODUCT_PRICE, $value, $msg);
    }

    public static function productPriceProvider(): array
    {
        return [
            '未入力' => ['', '商品価格は必ず入力してください。'],
            '文字列' => ['1aaaa', '商品価格は半角数字を入力してください。'],
            '全角数字' => ['１２３', '商品価格は半角数字を入力してください。'],
            '50未満' => ['49', '商品価格は50以上の数字を入力してください。'],
        ];
    }

    #[Test]
    #[TestDox('クーポン割引額チェック')]
    #[DataProvider('coupon')]
    public function ngCoupon(string $value, string $msg) {
        $this->verifyItemError(SC::DISCOUNT_AMOUNT, $value, $msg);
    }

    public static function coupon() {
        return [
            '未入力' => ['', 'クーポン割引額は必ず入力してください。'],
            '文字列' => ['1aaaa', 'クーポン割引額は半角数字を入力してください。'],
            '全角数字' =>['１２３', 'クーポン割引額は半角数字を入力してください。'],
            '0未満' =>['-1', 'クーポン割引額は0以上の数字を入力してください。'],
        ];
    }

    /**
     * 購入者情報に関するエラーを検証する。
     *
     * @param string $key
     * @param string $value
     * @param string $msg
     * @return void
     */
    private function verifyBuyerError(string $key, string $value, string $msg) {
        $record = $this->createBuyerInfo();
        $record[$key] = $value;
        $this->verifyError($record, $msg);
    }

    /**
     * 商品情報に関するエラーを検証する。
     *
     * @param string $key
     * @param string $value
     * @param string $msg
     * @return void
     */
    private function verifyItemError(string $key, string $value, string $msg) {
        $record = $this->createBuyerInfo();
        $record[SC::ITEMS][0][$key] = $value;
        $this->verifyError($record, $msg);
    }

    /**
     * バリデーションエラーについて検証する。
     *
     * @param array $record
     * @param string $msg
     * @return void
     */
    public function verifyError(array $record, string $msg) {
        $errors = $this->validate($record);
        $this->assertFalse(empty($errors), 'バリデーションエラーの有無');
        $expected = [EC::ROW => 2, EC::MSG => $msg];
        $this->assertSame($expected, current($errors));
    }

    /**
     * バリデーションチェックを実行する。
     *
     * @param array $record
     * @return array
     */
    public function validate(array $record):array {
        $validator = new ShiptValidator();
        $item = current($record[SC::ITEMS]);
        $record = array_merge($record, $item);
        unset($record[SC::ITEMS]);

        $record[SC::PRODUCT_ID] = $record[GlobalConstant::ID];
        unset($record[GlobalConstant::ID]);
        $error = $validator->validate([$record]);
        return $error;
    }

    /**
     * ランダムで注文情報を作成する。
     *
     * @return array
     */
    public function createBuyerInfo() {
        $buyer = ShiptLogTestHelper::createBuyerInfoOnly(TestDateUtil::formatToday());
        $item =  [GlobalConstant::ID => fake()->randomNumber(), SC::PRODUCT_NAME => fake()->text(50),
                    StockpileHeader::QUANTITY => fake()->numberBetween(1, 10),
                    SC::PRODUCT_PRICE => fake()->numberBetween(300, 10000), SC::DISCOUNT_AMOUNT => 0];
        $buyer[SC::ITEMS] = [$item];
        return $buyer;
        }
}
