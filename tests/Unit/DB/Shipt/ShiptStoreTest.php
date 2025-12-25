<?php

namespace Tests\Unit\DB\Shipt;

use App\Http\Controllers\ShiptLogController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * 出荷情報登録のテストクラス
 *
 */
#[CoversClass(ShiptLogController::class)]
class ShiptStoreTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->post('api/shipping', [
            'shipt_date' => '2024-01-01',
            'tracking_number' => '1234567890',
            'vendor_type_id' => 1,
            'items' => [
                [
                    'card_id' => 1,
                    'quantity' => 2,
                    'price' => 10.00,
                ],
                [
                    'card_id' => 2,
                    'quantity' => 1,
                    'price' => 20.00,
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}
