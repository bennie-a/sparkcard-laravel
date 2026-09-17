<?php

namespace Tests\Unit\Request\Upload;
use App\Http\Requests\CardFileRequest;
use App\Services\Constant\CardConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use App\Services\Constant\StockpileHeader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[TestDox("CardFileRequestに関するバリデータテスト")]
#[CoversClass(CardFileRequest::class)]
class CardFileRequestTest extends TestCase
{
    #[Test]
    #[TestDox("正常なJSONファイルをアップロードするとエラーが発生しないことを検証する。")]
    public function ok()
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

    #[Test]
    #[TestDox("tokens要素が存在してもエラーが発生しないことを検証する。")]
    public function ok_tokens()
    {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $json = file_get_contents(storage_path("test/json/fra.json"));
        $json_decode = json_decode($json, true);
        $query = [StockpileHeader::SETCODE =>'FRA',
                        'data' => ['cards' => $json_decode['data']['cards'], 'code' => $json_decode['data']['code']]];
        $validator = Validator::make($query, $rules);
        $isOk = $validator->passes();
        logger()->debug($validator->errors());
        $this->assertTrue($isOk);
    }

    #[TestDox("data要素が存在しない場合エラーが発生する。")]
    public function test_data_required()
    {
        $query = [StockpileHeader::SETCODE =>'BRO', 'xxxx' => ['cards'=> [], 'code'=>'BRO']];
        $this->ng($query);
    }


    #[TestDox("card要素の内容が空である場合エラーが発生する。")]
    public function test_cards_empty()
    {
        $query = [StockpileHeader::SETCODE =>'BRO', 'data' => ['cards'=> [], 'code'=>'BRO']];
        $this->ng($query);
    }

    #[TestDox("code要素が無い場合エラーが発生する。")]
    public function test_code_required()
    {
        $query = [StockpileHeader::SETCODE =>'BRO', 'data' => ['cards'=> ['name' => 'aaaa']]];
        $this->ng($query);
    }

#[TestDox("setCode要素が無い場合エラーが発生する。")]
    public function test_setcode_required()
    {
        $query = ['data' => ['cards'=> ['name' => 'aaaa']]];
        $this->ng($query);
    }


    private function ng(array $query) {
        $request = new CardFileRequest();
        $rules = $request->rules();
        $validator = Validator::make($query, $rules);
        $isFail = $validator->fails();
        $this->assertTrue($isFail);

    }
}
