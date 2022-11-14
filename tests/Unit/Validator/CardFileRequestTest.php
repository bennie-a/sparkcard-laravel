<?php

namespace Tests\Unit\Validator;

use App\Http\Requests\CardFileRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
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
        $json = file_get_contents(storage_path("test/json/test_color.json"));
        $json_decode = json_decode($json, true);
        $validator = Validator::make($json_decode, $rules);
        $isOk = $validator->passes();
        logger()->debug($validator->errors());
        $this->assertTrue($isOk);
    }

    public function test_data_required() 
    {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $validator = Validator::make(['xxxx' => ['cards'=> [], 'code'=>'BRO']], $rules);
        $isFail = $validator->fails();
        $this->assertTrue($isFail);
    }


    public function test_cards_empty() 
    {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $validator = Validator::make(['data' => ['cards'=> [], 'code'=>'BRO']], $rules);
        $isFail = $validator->fails();
        $this->assertTrue($isFail);
    }

    public function test_code_required()
    {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $validator = Validator::make(['data' => ['cards'=> ['name' => 'aaaa']]], $rules);
        $isFail = $validator->fails();
        $this->assertTrue($isFail);
    }
}
