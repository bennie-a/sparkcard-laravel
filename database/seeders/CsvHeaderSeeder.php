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
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_3', 'order_id' => $order++]); 
         CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_4', 'order_id' => $order++]); 
         CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_5', 'order_id' => $order++]); 
         CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_6', 'order_id' => $order++]); 
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_7', 'order_id' => $order++]); 
         CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_8', 'order_id' => $order++]); 
         CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_9', 'order_id' => $order++]); 
         CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_10', 'order_id' => $order++]); 
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品名', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品説明', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_種類', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_在庫数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_商品管理コード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_JANコード', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'ブランドID', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '販売価格', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'カテゴリID', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品の状態', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '配送方法', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '発送元の地域', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '発送までの日数', 'order_id' => $order++]);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品ステータス', 'order_id' => $order++]);

    }
}
