<?php

namespace Tests\Unit\Validator;

use App\Http\Requests\ArrivalRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ArrivalRequestTest extends TestCase
{
    /**
     * OKテスト
     * @dataProvider vendor_okprovider
     */
    public function test_vendor_ok(int $vender_id, string $vendor): void
    {
        $validator =$this->execute($vender_id, $vendor);
        $isOk = $validator->passes();
        $this->assertTrue($isOk);
    }

    /**
     * Undocumented function
     *@dataProvider vendor_ngprovider
     * @param integer $vender_id
     * @param string $vendor
     * @return void
     */
    public function test_vendor_ng(int $vender_id, string $vendor): void
    {
        $validator =$this->execute($vender_id, $vendor);
        $isOk = $validator->fails();
        $this->assertTrue($isOk);

        logger()->debug($validator->errors());
    }

    private function execute(int $vender_id, string $vendor) {
        $request = new ArrivalRequest();
        $rules = $request->rules();
        $data = [
            'card_id' => '2',
            'language' =>'JP',
            'cost' =>'28',
            'vendor_type_id' => $vender_id,
            'vendor' =>$vendor ,
            'market_price' =>'400',
            'quantity' =>'1',
            'condition' =>'NM',
            'arrival_date' => '2024/10/9'
        ];
        $validator = Validator::make($data, $rules);
        return $validator;
    }

    public function vendor_okprovider() {
        return [
            'オリジナルパック' => [1, ''],
            '私物' => [2, ''],
            '買取' => [3, '晴れる屋'],
            '棚卸し' => [4, ''],
            '返品' => [5, ''],
        ];
    }

    public function vendor_ngprovider() {
        return [
            '店舗購入' => [3, ''],
        ];
    }

}
