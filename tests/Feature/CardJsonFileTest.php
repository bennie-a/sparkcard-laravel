<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CardJsonFileTest extends TestCase
{
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function test_読み込み()
    {
        // $response = $this->get('/');
        $file = file(storage_path("test/json/test_color.json"));
        $header = ["Content-Type" => "application/json"];
        $response = $this->post('api/upload/card', ["data" => $file], $header);
        ;
        $response->assertStatus(201);
    }
}
