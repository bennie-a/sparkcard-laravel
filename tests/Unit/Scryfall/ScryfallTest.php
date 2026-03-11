<?php

namespace Tests\Feature;

use App\Api\Client\ScryfallClient;
use App\Enum\ExternalApi;
use App\Http\Controllers\ScryfallController;
use App\Services\Constant\CardConstant as Con;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as Header;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

#[CoversMethod(ScryfallController::class, 'index')]
class ScryfallTest extends TestCase
{
    #[Test]
    #[TestWith(["ja", '辺境地の御目付役、アジャニ', 'https://cards.scryfall.io/png/front/8/a/8afd2b17-a53f-421a-a61e-2957973cf860.png?1770090933'], '日本語')]
    #[TestWith(["en", '', 'https://cards.scryfall.io/png/front/6/1/6124a691-ae83-4d22-a177-0aee65b47064.png?1767951721'], '英語')]
    public function 言語を指定(string $lang, string $jpname, string $imageUrl) {
        $response = $this->ok('ECL', '4', $lang);
        $response->assertJson(function ($json) use ($jpname, $imageUrl) {
            $json->whereAll([
                GCon::NAME => $jpname,
                Con::EN_NAME => 'Ajani, Outland Chaperone',
                Con::IMAGE_URL => $imageUrl,
            ]);
        });
    }

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

    public function ok(string $setcode, string $number, string $lang)
    {
        $query = [Header::SETCODE => $setcode, Con::NUMBER => $number, Header::LANGUAGE => $lang];
        $response = $this->call('GET', '/api/scryfall', $query);
        $response->assertOk();
        return $response;
    }
}
