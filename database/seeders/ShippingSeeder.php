<?php

namespace Database\Seeders;

use App\Models\Shipping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shipping::create([
            'notion_id' => '118795084f9b806f9885c55677cd345d',
            'name' => 'ミニレター',
            'price' => 85]);
        Shipping::create([
            'notion_id' => 'c8ca57d7d07347cab12e2af68f76bbe6',
            'name' => '簡易書留',
            'price' => 460]);
        Shipping::create([
            'notion_id' => '29c8c95ed21645909cafea172a5dd2f7',
            'name' => 'クリックポスト',
            'price' => 185]);
    }
}
