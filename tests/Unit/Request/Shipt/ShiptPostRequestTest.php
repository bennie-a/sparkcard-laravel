<?php
namespace Tests\Unit\Request\Shipt;
use App\Http\Requests\Shipt\ShiptPostRequest;
use App\Http\Requests\Shipt\ShiptStoreRequest;
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
