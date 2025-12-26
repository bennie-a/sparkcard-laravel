<?php

use App\Http\Requests\Shipt\ShiptStoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Tests\Unit\Request\AbstractValidationTest;

class ShiptStoreRequestTest extends AbstractValidationTest {

    public function ok() {
        $this->ok_pattern([
            'order_id' => '123456',
            'buyer' => '山田太郎',
            'shipping_date' => '2024/06/15',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区西新宿2-8-1',
            'items' => [
                [
                    'id' => 1,
                    'shipment' => 2,
                    'total_price' => 500,
                    'single_price' => 250
                ],
                [
                    'id' => 2,
                    'shipment' => 1,
                    'total_price' => 300,
                    'single_price' => 300
                ]
            ]
        ]);
    }

    protected function createRequest(): FormRequest
    {
        return new ShiptStoreRequest();
    }
}
