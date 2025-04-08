<?php

namespace Tests\Unit\Validator;

use App\Http\Requests\CardFileRequest;
use App\Services\Constant\CardConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use App\Services\Constant\StockpileHeader;
use Tests\TestCase;

class CardFileRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_OK()
    {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $json = file_get_contents(storage_path("test/json/bro.json"));
        $json_decode = json_decode($json, true);
        $query = [StockpileHeader::SETCODE =>'BRO', 
                        'data' => ['cards' => $json_decode['data']['cards'], 'code' => $json_decode['data']['code']]];
        $validator = Validator::make($query, $rules);
        $isOk = $validator->passes();
        logger()->debug($validator->errors());
        $this->assertTrue($isOk);
    }

    public function test_data_required() 
    {
        $query = [StockpileHeader::SETCODE =>'BRO', 'xxxx' => ['cards'=> [], 'code'=>'BRO']];
        $this->ng($query);
    }


    public function test_cards_empty() 
    {
        $query = [StockpileHeader::SETCODE =>'BRO', 'data' => ['cards'=> [], 'code'=>'BRO']];
        $this->ng($query);
    }

    public function test_code_required()
    {
        $query = [StockpileHeader::SETCODE =>'BRO', 'data' => ['cards'=> ['name' => 'aaaa']]];
        $this->ng($query);
    }

    public function test_setcode_required()
    {
        $query = ['data' => ['cards'=> ['name' => 'aaaa']]];
        $this->ng($query);
    }


    private function ng($query) {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $validator = Validator::make($query, $rules);
        $isFail = $validator->fails();
        $this->assertTrue($isFail);

    }
}
