<?php

namespace Tests\Unit\Request;

use App\Http\Requests\PostCardDBRequest;
use App\Services\Constant\CardConstant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\StockpileHeader as Header;
use Tests\Trait\PostApiAssertions;

class PostCardRequestTest extends AbstractValidationTest
{
    use PostApiAssertions;
    /**
     * A basic feature test example.
     */
    public function test_ok(): void
    {
        $c = $this->getData();
        $this->ok_pattern($c);
    }

    /**
     * ScryfallIDに関するテストケース
     *
     * @dataProvider scryfallIdProvider
     * @param string $id
     * @param string $msg
     * @return void
     */
    public function test_ng_scryfallId(string $id, string $msg) {
        $c = $this->getData();
        $c[CardConstant::SCRYFALLID] = $id;
        $this->ng_pattern($c, [CardConstant::SCRYFALLID => $msg]);
    }

    private function scryfallIdProvider() {
        return [
            'ScryfallIDの形式が不正' =>['aaa', 'Scryfall IDの形式が異なります。'],
            'ScryfallIDが未入力' =>['', 'Scryfall ID / Multiverse ID / 画像URLのどれかを入力してください。'],
        ];
    }
    
    private function getData() {
        $contents = file_get_contents(storage_path("test/json/tdm.json"));
        $json = json_decode($contents, true);
        $query = sprintf('?setcode=TDM');
        $data =  ['data' => ['cards'=> $json['data']['cards'], 'code'=>$json['data']['code']]];
        $response = $this->upload_OK($query, $data);
        
        $cards = $response->json('cards');
        $c = current($cards);
        logger()->debug($c);
        return $c;
    }

    protected function createRequest(): FormRequest
    {
        return new PostCardDBRequest();
    }

        /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    public function getEndPoint() {
        return '/api/upload/card';
    }
}
