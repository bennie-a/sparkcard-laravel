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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * 注文CSVのValidatorクラスのテスト
 */
#[CoversClass(ShiptValidator::class)]
class ShiptValidatorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    #[TestDox('バリデーションチェックOK')]
    #[TestWith(['', ''], '全て入力済み')]
    #[TestWith([SC::SHIPPING_DATE, ''], '発送日が未入力')]
    #[TestWith([SC::ADDRESS_2, ''], '住所2が未入力')]
    #[TestWith([SC::CITY, '○○市'], '市町村名が市名')]
    #[TestWith([SC::CITY, '○○区'], '市町村名が区名')]
    #[TestWith([SC::CITY, '○○郡'], '市町村名が郡名')]
    #[TestWith([SC::CITY, '○○町'], '市町村名が町名')]
    #[TestWith([SC::CITY, '○○村'], '市町村名が村名')]
    public function Ok(string $key, string $value): void
    {
        $record = ShiptLogTestHelper::createTodayOrderInfos();
        if ($key) {
            $record[$key] = $value;
        }

        $error = $this->validate($record);
        $this->assertTrue(empty($error), 'バリデーションエラーの有無');
    }

    #[Test]
    #[TestDox('郵便番号チェック')]
    #[TestWith(['', 'required'], '未入力')]
    #[TestWith(['aa', 'shipping_postal_code.regex'], '郵便番号が文字列')]
    public function ngPostalCode(string $value, string $msgkey) {
        $msg = __("validation.{$msgkey}", ['attribute' => '郵便番号']);
        $this->verifyBuyerError(SC::POSTAL_CODE, $value, $msg);
    }

    #[Test]
    #[TestDox('住所チェック')]
    #[TestWith([SC::STATE, '', 'required'], '都道府県が未入力')]
    #[TestWith([SC::STATE, 'アメリカ', 'in'], '都道府県が47都道府県以外')]
    #[TestWith([SC::CITY, '', 'required'], '市区町村名が未入力')]
    #[TestWith([SC::CITY, 'aaaa', 'shipping_city.regex'], '市区町村名が市区町村以外の文字列')]
    #[TestWith([SC::ADDRESS_1, '', 'required'], 'その他住所1が未入力')]
    public function ngAddress(string $key, string $value, string $msgkey) {
        $attr = $this->getAttribute($key);
        $msg = $this->getMsg($msgkey, $attr);
        $this->verifyBuyerError($key, $value, $msg);
    }

    #[TestDox('商品コードチェック')]
    #[TestWith(['', 'required'], '未入力')]
    public function ngProductId(string $value, string $msgkey) {
        $msg = $this->getMsg($msgkey, '商品コード');
        $this->verifyBuyerError(SC::PRODUCT_ID, $value, $msg);
    }

    /**
     * 購入者情報に関するエラーを検証する。
     *
     * @param string $key
     * @param string $value
     * @param string $msg
     * @return void
     */
    public function verifyBuyerError(string $key, string $value, string $msg) {
        $record = ShiptLogTestHelper::createTodayOrderInfos();
        if ($key) {
            $record[$key] = $value;
        }
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
        logger()->info(current($errors));
        $this->assertSame($expected, current($errors));
    }

    /**
     * 名称を取得する。
     *
     * @param string $key
     * @return string
     */
    private function getAttribute(string $key):string {
        $attr = __("validation.attributes.".$key);
        return $attr;
    }

    private function getMsg(string $msgkey, string $attribute) {
        $msg = __("validation.{$msgkey}", ['attribute' => $attribute]);
        return $msg;
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
}
