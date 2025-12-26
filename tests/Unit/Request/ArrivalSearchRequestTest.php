<?php

namespace Tests\Unit\Request;

use App\Http\Requests\Arrival\ArrivalSearchRequest;
use App\Services\Constant\SearchConstant as Con;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 出荷情報検索機能のリクエストのテスト
 */
class ArrivalSearchRequestTest extends AbstractValidationTest
{

    /**
     * OKパターン
     *@dataProvider okdata
     * @return void
     */
    public function test_ok(array $data) {
        $this->ok_pattern($data);
    }

    protected function okdata() {
        return [
            '全項目入力' =>[ [Con::CARD_NAME => 'ドラゴン', Con::START_DATE => '2025/03/01', Con::END_DATE => '2025/03/10']],
            'カード名のみ' =>[ [Con::CARD_NAME => 'ドラゴン']],
            '入荷日(開始日)のみ' =>[[Con::START_DATE => '2025/03/10']],
            '入荷日(終了日)のみ' =>[[Con::END_DATE => '2025/03/10']],
            '入荷日(開始日)と入荷日(終了日)入力' =>[ [Con::START_DATE => '2025/03/01', Con::END_DATE => '2025/03/10']],
        ];
    }

    /**
     * NGパターン
     *
     * @param array $data
     * @param array $msgs
     * @dataProvider ngdata
     * @return void
     */
    public function test_ng(array $data, array $msgs) {
        $this->ng_pattern($data, $msgs);
    }
    public function ngdata() {
        return [
            'パラメータなし' =>
                            [[],
                                [Con::CARD_NAME =>
                                'カード名 / 入荷日(開始日) / 入荷日(終了日)の中で1個以上の項目を必ず入力してください。']]
        ];
    }

    protected function createRequest(): FormRequest
    {
        return new ArrivalSearchRequest();
    }
}
