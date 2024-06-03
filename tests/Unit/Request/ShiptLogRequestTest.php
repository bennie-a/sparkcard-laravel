<?php

namespace Tests\Unit\Request;

use App\Http\Requests\ShiptLogRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Services\Constant\StockpileHeader as Header;
use Carbon\Carbon;
use Illuminate\Validation\Validator as ValidationValidator;

use function PHPUnit\Framework\assertNotNull;

/**
 * 出荷情報検索機能のリクエストのテスト
 */
class ShiptLogRequestTest extends TestCase
{
    /**
     * OKパターン
     * @dataProvider okdata
     */
    public function test_ok(array $data): void
    {
        $validator = $this->validate($data);
        $isPassed = $validator->passes();
        $this->assertTrue($isPassed);
    }

    /**
     * NGパターン
     *
     * @param array $data
     * @dataProvider ngdata
     */
    public function test_ng(array $data, array $msgs) {
        $validator = $this->validate($data);
        $isPassed = $validator->passes();
        $this->assertFalse($isPassed);

        foreach($msgs as $key => $m) {
            $actual = $validator->messages()->get($key);
            $this->assertNotEmpty($actual, 'メッセージの有無');
            logger()->debug($actual);
            $this->assertEquals($m, $actual[0], 'メッセージの合致');
        };
    }
    
    /**
     * バリデーションチェックを実行する。
     *
     * @param array $data
     * @return ValidationValidator
     */
    private function validate(array $data) {
        
        $request = new ShiptLogRequest();
        $rules = $request->rules();
    
        $validator = Validator::make($data, $rules, $request->messages(), $request->attributes());
        return $validator;
    }

    protected function okdata() {
        return [
            '全項目入力' =>[ [Header::BUYER => '鈴木', Header::SHIPPING_DATE => '2024/06/03']],
            '発送日が今日' =>[ [Header::BUYER => '鈴木', Header::SHIPPING_DATE => Carbon::today()->format('Y-m-d')]],
            '発送日が昨日' =>[ [Header::BUYER => '鈴木', Header::SHIPPING_DATE => Carbon::yesterday()->format('Y-m-d')]],
            '全項目未入力' =>[ [Header::BUYER => '', Header::SHIPPING_DATE => '']],
            'パラメータなし' =>[ []],
        ];
    }

    protected function ngdata() {
        return [
            '発送日が日付形式ではない' =>[ [Header::BUYER => '', Header::SHIPPING_DATE => 'aaaa'],
             [Header::SHIPPING_DATE => '発送日が日付形式ではありません']],
             '発送日が明日以降' =>[ [Header::BUYER => '', Header::SHIPPING_DATE => Carbon::tomorrow()->format('Y-m-d')],
             [Header::SHIPPING_DATE => '発送日は今日以前の日付を入力してください。']]
        ];
    }
}
