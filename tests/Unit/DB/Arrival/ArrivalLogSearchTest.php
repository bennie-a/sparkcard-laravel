<?php

namespace Tests\Unit\DB\Arrival;

use App\Libs\MtgJsonUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\StockpileHeader as Header;

use function PHPUnit\Framework\assertTrue;

class ArrivalLogSearchTest extends ArrivalLogGroupingTest
{

        /**
     * OKパターン
     * @dataProvider okProvider
     */
    public function test_ok(array $condition) {
        $response = $this->assert_OK($condition);
        $json = $response->json();
        logger()->debug($json);
        // if (MtgJsonUtil::isEmpty(Con::START_DATE, $condition)) {
        //     $condition[Con::START_DATE] = $this->formatDate(self::three_days_before());
        // }
        
        // if (MtgJsonUtil::isEmpty(Con::END_DATE, $condition)) {
        //     $condition[Con::END_DATE] = $this->formatDate(self::today());
        // }
    }

    public function okProvider() {
        return [
            '入荷先カテゴリが買取以外' => 
                [[Header::ARRIVAL_DATE =>$this->formatToday(), Header::VENDOR_TYPE_ID => 1]],
        ];
    }
    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/arrival/filtering';
    }
}
