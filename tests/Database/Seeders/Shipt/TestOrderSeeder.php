<?php
namespace Tests\Database\Seeders\Shipt;

use Illuminate\Database\Seeder;
use Tests\Util\TestDateUtil;

class TestOrderSeeder extends Seeder
{
    public function run()
    {
        $platform = ['base', 'mercari'];
        $today = TestDateUtil::today();
        $rows = [];
        for ($i = 0; $i < 5; $i++) {
            $shiptDate = $today->subDays($i);
            $rows = ['platform', 'platform_order_id', 'buyer_name', 'zip_code', 'address', 'item_count',
                'items_subtotal ', 'coupon_discount', 'shipt_fee_id', 'grand_total', 'shipt_date'];

        }
    }
}
