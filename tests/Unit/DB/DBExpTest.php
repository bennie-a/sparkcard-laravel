<?php

namespace Tests\Unit\DB;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DBExpTest extends TestCase
{
    public function setup():void
    {
        parent::setUp();
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    public function test_全項目入力()
    {
        $storeData = ['id' => '008b0ade-0521-462a-b1b3-93c7e8c8406c',
         'name'=>'コールドスナップ',
         'attr' => 'CSP',
        'base_id' => 4517385,
        'release_date' => '2006-7-21'];
        $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function test_BASEIDが0()
    {
        $storeData = ['id' => '008b0ade-0521-462a-b1b3-93c7e8c8406c',
         'name'=>'コールドスナップ',
         'attr' => 'CSP',
        'base_id' => 0,
        'release_date' => '2006-7-21'];
        $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function test_BASEIDが未入力()
    {
        $storeData = ['id' => '008b0ade-0521-462a-b1b3-93c7e8c8406c',
         'name'=>'コールドスナップ',
         'attr' => 'CSP',
        'release_date' => '2006-7-21'];
        $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);
    }
}
