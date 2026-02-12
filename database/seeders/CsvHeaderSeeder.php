<?php

namespace Database\Seeders;

use App\Enum\CsvFlowType;
use App\Enum\ShopPlatform;
use App\Models\CsvHeader;
use Illuminate\Database\Seeder;
use App\Services\Constant\ShiptConstant as SC;

class CsvHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mercari_columns = [SC::ORDER_ID, SC::SHIPPING_DATE, SC::PRODUCT_ID, SC::BUYER, SC::PRODUCT_NAME, SC::QUANTITY,
                     SC::PRODUCT_PRICE, SC::POSTAL_CODE, SC::STATE, SC::CITY, SC::ADDRESS_1, SC::ADDRESS_2, sc::DISCOUNT_AMOUNT];
        for($i = 0; $i < count($mercari_columns); $i++) {
            CsvHeader::create(['shop' => ShopPlatform::MERCARI->value, 'csv_type' => CsvFlowType::SHIPT->value,
                                                 'column' => $mercari_columns[$i], SC::ORDER_ID => $i + 1]);
        }
    }
}
