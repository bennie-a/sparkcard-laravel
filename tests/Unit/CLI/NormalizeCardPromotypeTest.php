<?php

namespace Tests\Unit\CLI;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Database\Seeders\ExpansionSeeder;
use Database\Seeders\FoiltypeSeeder;
use Database\Seeders\MainColorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Database\Seeders\CLI\NormalizePromoSeeder;
use Tests\Database\Seeders\TestExpansionSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

/**
 * normalize:card-promotypeコマンドのテスト
 */
class NormalizeCardPromotypeTest extends TestCase
{

    public function setup(): void
    {
        parent::setUp();
        logger()->info('マスタデータ登録開始');
        $this->seed(TruncateAllTables::class);
        $this->seed(ExpansionSeeder::class);
        $this->seed(TestExpansionSeeder::class);
        $this->seed(MainColorSeeder::class);
        $this->seed(FoiltypeSeeder::class);
        $this->seed(NormalizePromoSeeder::class);
        logger()->info('マスタデータ登録終了');
    }
    /**
     * A basic feature test example.
     */
    public function test_プロモタイプが全てDBに存在する(): void
    {
        $types = Promotype::findBySetCode('MKM');
        $mkm = Expansion::findBySetCode('MKM');
        foreach ($types as $type) {
            $name = fake()->unique()->realText(10);
            if ($type->attr !== 'draft') {
                $name .= "≪{$type->name}≫";
            }
            CardInfo::factory()->createOne([
                GlobalConstant::NAME => $name,
                CardConstant::EXP_ID => $mkm->notion_id,
                CardConstant::IS_FOIL => false,
                'foiltype_id' => 1, // Non-foil
            ]);
        }
        $exitCode  = Artisan::call('normalize:card-promotype');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
    }
}
