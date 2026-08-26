<?php
namespace Tests\Database\Seeders\Shipt;

use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use Illuminate\Database\Seeder;
use App\Services\Constant\ShiptConstant as SCon;

/**
 * テスト用注文情報をランダムで生成するSeederクラス
 */
class TestOrderSeeder extends Seeder
{
    public function run()
    {
        Orders::factory()->count(10)->create()
            ->each(function (Orders $order) {
                OrderItem::factory()->count($order->item_count)->create([
                    SCon::ORDER_ID => $order->id,
                ]);
        });
    }
}
