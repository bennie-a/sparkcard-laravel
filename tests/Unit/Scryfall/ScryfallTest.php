<?php

namespace Tests\Feature;

use App\Services\Constant\CardConstant as Con;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as Header;

/**
 * ScryfallControllerのテスト
 */
class ScryfallTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_単色(): void
    {
        $query = [Header::SETCODE => 'IKO', Con::NUMBER => 1, Header::LANGUAGE => 'ja'];
        $response = $this->call('GET', '/api/scryfall', $query);
        $response->assertOk();
        $json = json_decode($response->content());
        $this->assertEquals(480816, $json->multiverse_id);
    }
}
