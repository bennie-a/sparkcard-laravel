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
        ];
    }
}
