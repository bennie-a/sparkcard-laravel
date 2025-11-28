<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
/**
 * 注文CSVのValidatorクラスのテスト
 */
class ShiptValidatorTest extends TestCase
{
    #[TestDox('出荷数のバリデーション確認')]
    #[TestWith(['1', '出荷数 > 在庫数'])]
    #[TestWith(['aa', '数値ではない'])]
    public function testShipment(string $shipment): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
