<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * 入荷手続きに関するテスト
 */
class ArrivalTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @dataProvider okprovider
     * @return void
     */
    public function test_ok(array $query)
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
}
