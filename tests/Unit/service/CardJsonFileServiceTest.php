<?php

namespace Tests\Unit\service;

use App\Services\CardJsonFileService;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

class CardJsonFileServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_エキスパンション名()
    {   
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        assertEquals("WAR", $result["setCode"]);
    }

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
        assertEquals("規律の絆", $card['name']);
        assertEquals("462253", $card['multiverseId']);
        assertEmpty($card['scryfallId']);
    }

    public function test_日本限定カード() {
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $card = $result["cards"][4];
        assertEquals("群れの声、アーリン" ,$card['name']);
        assertEmpty($card['multiverseId']);
        assertEquals('43261927-7655-474b-ac61-dfef9e63f428', $card['scryfallId'], 'scryfallId');
    }

    public function test_日本語表記なし()
    {
        $contents = file_get_contents(storage_path("test/json/test_color.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $card = current($result["cards"]);
        assertEquals("飛空士の騎兵部隊", $card['name'], 'カード名(日本語)');
        assertEmpty($card['multiverseId']);
        assertEmpty($card['scryfallId']);
    }
}
