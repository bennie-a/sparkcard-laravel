<?php

namespace Tests\Unit\CLI;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Models\Shipt\Orders;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Database\Seeders\ExpansionSeeder;
use Database\Seeders\FoiltypeSeeder;
use Database\Seeders\MainColorSeeder;
use Database\Seeders\ShippingSeeder;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\CLI\Divide\DivideShippingLogSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

#[TestDox('divide:orderコマンドのテスト')]
#[CoversClass(App\Console\Commands\DivideOrder::class)]
class DivideOrderTest extends TestCase
{

    public function setup(): void
    {
        parent::setUp();
        logger()->info('マスタデータ登録開始');
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(DivideShippingLogSeeder::class);
        logger()->info('マスタデータ登録終了');
    }

    #[Test]
    #[TestDox('divide:orderコマンドのテスト')]
    public function テスト実行(): void
    {
        // $cardNames = CardInfo::query()->orderBy('id')->get()->pluck('name')->toArray();
        $exitCode  = Artisan::call('divide:order');
        logger()->info(Artisan::output());
        // $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
        $this->assertDatabaseCount(Orders::class, 0);
        // $actuals = CardInfo::query()->whereNotNull('promotype_id')->get();
        // $this->assertCount(count($cardNames), $actuals, '分離した件数が正しくない');

        // foreach($actuals as $a) {
        //     $this->assertNotNull($a->promotype_id, 'プロモタイプIDがnullです。');
        //     $this->assertNotEmpty($a->name, 'カード名が空です。');
        //     $this->assertStringNotContainsString('≪', $a->name, 'カード名に≪が含まれています。');
        //     $this->assertStringNotContainsString('≫', $a->name, 'カード名に≫が含まれています。');

        //     $e = current($cardNames);
        //     $promo = Promotype::where(GlobalConstant::ID, $a->promotype_id)->first();
        //     if ($promo->attr === 'draft') {
        //         $this->assertEquals($e, $a->name, 'カード名が正しくありません。');
        //     } else {
        //         $this->assertStringContainsString($promo->name, $e, 'カード名にプロモタイプ名が含まれていません。');
        //     }

        //     next($cardNames);
        // };
    }

    public function test_プロモタイプが存在しない()  {
        $expectedName = fake()->unique()->realText(10);
        $promoName = '存在しないプロモ';
        $newname =  $expectedName. '≪'.$promoName.'≫';
        $neo = Expansion::findBySetCode('NEO');
        CardInfo::factory()->createOne([
            GlobalConstant::NAME => $newname,
            CardConstant::EXP_ID => $neo->notion_id,
            CardConstant::IS_FOIL => false,
            'foiltype_id' => 1, // Non-foil,
            CardConstant::PROMO_ID => null
        ]);
        $exitCode  = Artisan::call('divide:order');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
        $this->assertDatabaseHas('promotype', [
            GlobalConstant::NAME => $promoName
        ]);
        $actual = Promotype::findCardByName($promoName);
        $this->assertDatabaseHas('card_info', [
            GlobalConstant::NAME => $expectedName,
            CardConstant::PROMO_ID => $actual->id
        ]);
    }

    public function test_トークン() {
        $expectedName = fake()->unique()->realText(10);
        $promoName = 'トークンタイプ';
        $newname =  $expectedName. '≪'.$promoName.'≫';
        $neo = Expansion::findBySetCode('MKM');
        CardInfo::factory()->createOne([
            GlobalConstant::NAME => $newname,
            CardConstant::EXP_ID => $neo->notion_id,
            CardConstant::IS_FOIL => false,
            'foiltype_id' => 1, // Non-foil,
            'color_id' => 'T',
            CardConstant::PROMO_ID => null
        ]);
        $exitCode  = Artisan::call('normalize:card-promotype');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
        $this->assertDatabaseHas('card_info', [
            GlobalConstant::NAME => $newname,
            CardConstant::PROMO_ID => 1
        ]);
        $this->assertDatabaseMissing('promotype', [
            GlobalConstant::NAME => $promoName
        ]);
    }
}
