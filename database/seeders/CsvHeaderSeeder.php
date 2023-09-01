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
        CsvHeader::create(['shop' => 'base', 'column' =>  '商品ID']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '商品名']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '種類ID']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '種類名']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '説明']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '価格']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '税率']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '在庫数']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '公開状態']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '表示順']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '種類在庫数']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '画像1']);
        CsvHeader::create(['shop' => 'base', 'column' =>  '画像2']);

        CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_1']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品画像名_2']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品名']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品説明']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU1_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU2_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU3_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU4_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU5_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU6_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU7_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU8_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU9_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_種類']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_在庫数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_商品管理コード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'SKU10_JANコード']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'ブランドID']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '販売価格']);
        CsvHeader::create(['shop' => 'mercari', 'column' => 'カテゴリID']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品の状態']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '配送方法']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '発送元の地域']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '発送までの日数']);
        CsvHeader::create(['shop' => 'mercari', 'column' => '商品ステータス']);

    }
}
