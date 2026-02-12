<?php

namespace Tests\Feature;

use App\Services\Constant\CardConstant as Con;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as Header;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * ScryfallControllerのテスト
 */
class ScryfallTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    #[DataProvider('imageProvider')]
    public function test_画像(string $setcode, int $number, string $url): void
    {
        $query = [Header::SETCODE => $setcode, Con::NUMBER => $number, Header::LANGUAGE => 'ja'];
        $response = $this->call('GET', '/api/scryfall', $query);
        $response->assertOk();
        $response->assertJsonFragment(['imageurl' => $url]);
    }

    public static function imageProvider(): array
    {
        return [
            '表面のみ' => ['IKO', 1, 'https://cards.scryfall.io/png/front/e/1/e1059d5b-1de6-4988-a3a8-fe540a541342.png?1645734158'],
            '上下二面カード' => ['AKH', 210, 'https://cards.scryfall.io/png/front/4/c/4cffc5c9-0115-4a8f-9665-a3fbdd4179c2.png?1540281378'],
            '表裏両面カード' => ['MH3', 261, 'https://cards.scryfall.io/png/front/3/0/305ae3b5-7c12-43ad-b19f-dbd04c9afcf7.png?1730229671'],
        ];
    }
}
