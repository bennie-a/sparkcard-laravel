<?php
namespace Tests\Unit\Request\Shipt;

use App\Services\Constant\ShiptConstant as SC;
use App\Http\Requests\ShiptLogRequest;
use Illuminate\Foundation\Http\FormRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Unit\Request\AbstractValidationTest;
use Tests\Util\TestDateUtil;

#[TestDox('ShiptLogRequestクラスをテストするクラス')]
#[CoversClass(ShiptLogRequest::class)]
class ShiptLogRequestTest extends AbstractValidationTest
{
    #[Test]
    #[TestDox('全項目を入力した正常テスト')]
    public function ok_all(): void {
        $request = [SC::SHIPT_DATE => TestDateUtil::formatToday(), SC::BUYER => 'aaaa'];
        $this->ok_pattern($request);
    }

    #[Test]
    #[TestDox('発送日のみを入力した正常テスト')]
    public function ok_shipt_date(): void {
        $request = [SC::SHIPT_DATE => TestDateUtil::formatToday()];
        $this->ok_pattern($request);
    }

    #[Test]
    #[TestDox('購入者名を入力した正常テスト')]
    public function ok_buyer_name(): void {
        $request = [SC::BUYER => 'aaaa'];
        $this->ok_pattern($request);
    }

    #[Test]
    #[TestDox('全項目未入力の場合のエラーテスト')]
    public function ng_noInput(): void {
        $request = [];
        $this->ng_pattern($request, [SC::BUYER => '購入者名、または発送日のどちらかを入力してください。']);
    }

    protected function createRequest(): FormRequest
    {
        return new ShiptLogRequest();
    }

}
