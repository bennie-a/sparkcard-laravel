<?php

namespace Database\Seeders;

use App\Models\CsvHeader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CsvHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order = 0;
        CsvHeader::create(['shop' => 'base', 'column' =>  '商品ID', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '商品名', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '種類ID', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '種類名', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '説明', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '価格', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '税率', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '公開状態', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '表示順', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '種類在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '画像1', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'base', 'column' =>  '画像2', 'order_id' => $order++]);

        $order = 0;
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_1', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_2', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品名', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品説明', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '販売価格', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'カテゴリID', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品の状態', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '配送方法', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '発送元の地域', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '発送までの日数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品ステータス', 'order_id' => $order++]);

    }
}
