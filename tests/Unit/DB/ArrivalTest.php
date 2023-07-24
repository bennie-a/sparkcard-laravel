<?php

namespace Tests\Unit\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * 入荷手続きに関するテスト
 */
class ArrivalTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestExpansionSeeder');
        $this->seed('MainColorSeeder');
        $this->seed('ShippingSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
    }
    /**
     * A basic feature test example.
     *
     * 
     * @return void
     */
    public function test_ok()
    {
        $response = $this->post('/api/arrival');

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * OKケース
     *
     * @return void
     */
    public function okprovider() {

    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }
}
