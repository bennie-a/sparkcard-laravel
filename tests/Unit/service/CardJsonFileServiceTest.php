<?php

namespace Tests\Unit\service;

use App\Services\CardJsonFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class CardJsonFileServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_日本語表記あり()
    {   
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $card = current($result["cards"]);
        assertEquals("規律の絆",$card['name']);
    }
}
