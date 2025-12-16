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
    #[TestWith(['', 'required'], '郵便番号が未入力')]
    #[TestWith(['aa', 'shipping_postal_code.regex'], '郵便番号が文字列')]
    public function ngPostalCode(string $value, string $msgkey) {
        $attr = __("validation.attributes.".SC::POSTAL_CODE);
        $msg = __("validation.{$msgkey}", ['attribute' => $attr]);
        $this->verifyError(SC::POSTAL_CODE, $value, $msg);
    }

    public function verifyError(string $key, string $value, string $msg) {
        $record = ShiptLogTestHelper::createTodayOrderInfos();
        if ($key) {
            $record[$key] = $value;
        }
        $errors = $this->validate($record);
        $this->assertFalse(empty($errors), 'バリデーションエラーの有無');
        $expected = [EC::ROW => 2, EC::MSG => $msg];
        logger()->info(current($errors));
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
}
