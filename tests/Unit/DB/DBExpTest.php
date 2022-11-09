<?php

namespace Tests\Unit\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class DBExpTest extends TestCase
{
    public function setup():void
    {
        parent::setUp();
    }
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_全項目入力()
    {
        // $response = $this->get('/');
        $storeData = ['id' => '008b0ade-0521-462a-b1b3-93c7e8c8406c',
         'name'=>'コールドスナップ',
         'attr' => 'CSP',
        'base_id' => 4517385,
        'release_date' => '2006-7-21'];
        $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);

        // $response->assertStatus(200);
    }
}
