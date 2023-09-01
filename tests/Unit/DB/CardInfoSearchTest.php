<?php

namespace Tests\Unit\DB;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\SearchConstant as Con;
/**
 * カード情報検索に関するテスト
 */
class CardInfoSearchTest extends TestCase
{
    use RefreshDatabase;
    public function setup():void
    {
        parent::setup();
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
    }
    /**
     * A basic feature test example.
     * @dataProvider okprovider
     * @return void
     */
    public function test_ok(array $condition)
    {
        $contents = $this->execute($condition);
    }

    public function execute(array $condition, int $statuscode = 200) {
        $query = ['name' => $condition[0], 'set' =>$condition [1], 'color' => $condition[2], 'isFoil' => $condition[3]];
        $response = $this->json('GET', 'api/database/card', $query);
        $response->assertStatus($statuscode);
        $json = $response->baseResponse->getContent();
        return  json_decode($json, true);
    }

    public function okprovider() {
        return [
            'カード名のみ' => [['エリシュ・ノーン', '', '', false]]
            // 'セット名のみ' => [],
            // '色のみ' => [],
            // 'Foilのみ' => [],
            // '全入力' => []
        ];
    }
}
