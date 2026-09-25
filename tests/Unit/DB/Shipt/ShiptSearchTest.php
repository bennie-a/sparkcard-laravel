<?php
namespace Tests\Unit\DB\Shipt;

use App\Http\Controllers\ShiptLogController;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;
use Tests\Trait\GetApiAssertions;
use App\Services\Constant\ShiptConstant as SC;
use PHPUnit\Framework\Attributes\Test;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;

#[TestDox('注文情報検索機能のテスト')]
#[CoversClass(ShiptLogController::class)]
class ShiptSearchTest extends TestCase
{
    use GetApiAssertions;

    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestOrderSeeder::class);
    }

    #[Test]
    #[TestDox('日付を指定した検索を検証する')]
    public function ok()
    {
        $this->assert_OK([SC::SHIPT_DATE => '2026/9/23']);
    }

    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/shipping';
    }
}
