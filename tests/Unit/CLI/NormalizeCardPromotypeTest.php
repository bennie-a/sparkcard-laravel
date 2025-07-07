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
use Illuminate\Support\Facades\Artisan;
use Tests\Database\Seeders\CLI\Normalize\NormCardInfoSeeder;
use Tests\Database\Seeders\CLI\Normalize\NormPromoSeeder;
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
        $this->seed(NormPromoSeeder::class);
        $this->seed(NormCardInfoSeeder::class);
        logger()->info('マスタデータ登録終了');
    }
    /**
     * A basic feature test example.
     */
    public function test_プロモタイプが全てDBに存在する(): void
    {
        $cardNames = CardInfo::query()->orderBy('id')->get()->pluck('name')->toArray();
        $exitCode  = Artisan::call('normalize:card-promotype');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');

        $actuals = CardInfo::query()->whereNotNull('promotype_id')->get();
        $this->assertCount(count($cardNames), $actuals, '分離した件数が正しくない');

        foreach($actuals as $a) {
            $this->assertNotNull($a->promotype_id, 'プロモタイプIDがnullです。');
            $this->assertNotEmpty($a->name, 'カード名が空です。');
            $this->assertStringNotContainsString('≪', $a->name, 'カード名に≪が含まれています。');
            $this->assertStringNotContainsString('≫', $a->name, 'カード名に≫が含まれています。');

            $e = current($cardNames);
            $promo = Promotype::where(GlobalConstant::ID, $a->promotype_id)->first();
            if ($promo->attr === 'draft') {
                $this->assertEquals($e, $a->name, 'カード名が正しくありません。');
            } else {
                $this->assertStringContainsString($promo->name, $e, 'カード名にプロモタイプ名が含まれていません。');
            }
            
            next($cardNames);
        };
    }

    public function test_プロモタイプが一部存在しない(): void
    {
        $failname = fake()->unique()->realText(10) . '≪存在しないプロモ≫';
        $neo = Expansion::findBySetCode('NEO');
        CardInfo::factory()->createOne([
            GlobalConstant::NAME => $failname,
            CardConstant::EXP_ID => $neo->notion_id,
            CardConstant::IS_FOIL => false,
            'foiltype_id' => 1, // Non-foil
        ]);
        $mkm = Expansion::findBySetCode('MKM');
        $mkms = CardInfo::query()->where(CardConstant::EXP_ID, $mkm->notion_id)
                                                        ->orderBy(GlobalConstant::ID)->get()->pluck(GlobalConstant::NAME)->toArray();
        $exitCode  = Artisan::call('normalize:card-promotype');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');

        $actuals = CardInfo::query()->whereNotNull('promotype_id')->get();
        $this->assertCount(count($mkms), $actuals, '分離した件数が正しくない');

        foreach($actuals as $a) {
            $this->assertNotNull($a->promotype_id, 'プロモタイプIDがnullです。');
            $this->assertNotEmpty($a->name, 'カード名が空です。');
            $this->assertStringNotContainsString('≪', $a->name, 'カード名に≪が含まれています。');
            $this->assertStringNotContainsString('≫', $a->name, 'カード名に≫が含まれています。');

            $e = current($mkms);
            $promo = Promotype::where(GlobalConstant::ID, $a->promotype_id)->first();
            if ($promo->attr === 'draft') {
                $this->assertEquals($e, $a->name, 'カード名が正しくありません。');
            } else {
                $this->assertStringContainsString($promo->name, $e, 'カード名にプロモタイプ名が含まれていません。');
            }
            
            next($mkms);
        };

        $ngInfo = CardInfo::query()->whereNull('promotype_id')->get();
        $this->assertCount(1, $ngInfo, 'プロモタイプが存在しないカード情報が残っています。');
        $this->assertEquals($failname, $ngInfo[0]->name, 'プロモタイプが存在しないカード情報の名前が正しくありません。');
    }
}
