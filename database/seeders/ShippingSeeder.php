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
            'notion_id' => 'e7db5d1cf759498fb66bac08644885da',
            'name' => 'ミニレター',
            'price' => 63]);
        Shipping::create([
            'notion_id' => 'c8ca57d7d07347cab12e2af68f76bbe6',
            'name' => '簡易書留',
            'price' => 404]);
        Shipping::create([
            'notion_id' => '29c8c95ed21645909cafea172a5dd2f7',
            'name' => 'クリックポスト',
            'price' => 185]);
    }
}
