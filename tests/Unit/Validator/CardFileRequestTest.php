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
    public function test_example()
    {
        $request = new CardFileRequest();
        $rules = $request->rules();
        // $json = file_get_contents(storage_path("test/json/test_color.json"));
        // $json_decode = json_decode($json);
        $validator = Validator::make(['data' => ['cards'=> ['name' => 'aaa'], 'code'=>'BRO']], $rules);
        $isOk = $validator->passes();
        logger()->debug($validator->errors());
        $this->assertTrue($isOk);
        // $response = $this->get('/');

        // $response->assertStatus(200);
    }
}
