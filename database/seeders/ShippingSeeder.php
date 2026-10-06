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
            'price' => 85,
            'deleted' => false]);
        Shipping::create([
            'notion_id' => 'c8ca57d7d07347cab12e2af68f76bbe6',
            'name' => '簡易書留',
            'price' => 460,
            'deleted' => false]);
        Shipping::create([
            'notion_id' => '29c8c95ed21645909cafea172a5dd2f7',
            'name' => 'クリックポスト',
            'price' => 185,
            'deleted' => true]);
        Shipping::create([
            'notion_id' => '3f0795084f9b80738b3be4f623c82ee4',
            'name' => 'クリックポスト',
            'price' => 240,
            'deleted' => false]);
        Shipping::create([
            'notion_id' => '9c846648c55847b883773aee427b8282',
            'name' => 'らくらくメルカリ便',
            'price' => 210,
            'deleted' => false]);
        Shipping::create([
            'notion_id' => 'db4096ad9b934329927816088cd110b0',
            'name' => 'ネコポス',
            'price' => 190,
            'deleted' => false]);
        Shipping::create([
            'notion_id' => '477121ec55fa42b6926bcc9b61c1028d',
            'name' => 'ゆうパケットポストmini',
            'price' => 220,
            'deleted' => false]);
    }
}
