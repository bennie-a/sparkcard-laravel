<?php

namespace Database\Seeders;

use App\Models\Expansion;
use App\Models\Stockpile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestExpansionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('promotype')->truncate();
        Expansion::create(['attr' => '4ED', 'name' => '第4版', 'notion_id' => 'f01d4e05-600a-4f30-bc15-9633b98ca8c7', 'release_date' => '1995-04-01']);
        Expansion::create(['attr' => 'BRO', 'name' => '兄弟戦争', 'notion_id' => 'd4d9832d-3bb3-4e07-be26-6a37ec198991', 'release_date' => '2022-11-08']);
        Expansion::create(['attr' => 'WAR', 'name' => '灯争大戦', 'notion_id' => '80a7660b-3de0-4ce1-8e51-1e90e123faae', 'release_date' => '2022-11-08']);
        Expansion::create(['name' => '神河：輝ける世界', 'attr' => 'NEO', 'notion_id' => '92f67d9b-aae6-4df7-9ca0-18f00fc9c7f5', 'release_date' => '2022-02-08']);
        Expansion::create(['attr' => 'ONE', 'name' => 'ファイレクシア：完全なる統一', 'notion_id' => '1a36d935-ee60-466b-ac43-ecc797c0ee87', 'release_date' => '2023-02-03']);
        Expansion::create(['attr' => 'DMU', 'name' => '団結のドミナリア', 'notion_id' => 'e72e96b4-2457-4e96-82a6-264d7e7731ec', 'release_date' => '2022-09-09']);
        Expansion::create(['attr' => 'MIR', 'name' => 'ミラージュ', 'notion_id' => 'd87da876-9912-46ce-9334-fa05bc859f27', 'release_date' => '1996-10-08']);
        Expansion::create(['attr' => 'MUL', 'name' => '機械兵団の進軍_多元宇宙の伝説', 'notion_id' => 'd4a09cf4-d115-44d6-9cbe-ba4bda78e737', 'release_date' => '2023-04-21']);
        Expansion::create(['attr' => 'MOM', 'name' => '機械兵団の進軍', 'notion_id' => 'f9ee51b8-4a57-4dc9-b29e-f85076dc0b62', 'release_date' => '2023-04-21']);
        Expansion::create(['attr' => 'WOE', 'name' => 'エルドレインの森', 'notion_id' => '0ad5f0d5-101e-44e8-8212-8116c7170274', 'release_date' => '2023-09-08']);
        Expansion::create(['attr' => 'WOT', 'name' => 'エルドレインの森おとぎ話カード', 'notion_id' => 'b5216886-c859-4a6b-87e6-b3ecae203dfb', 'release_date' => '2023-09-08']);
        Expansion::create(['attr' => 'SCH', 'name' => 'Store Championships', 'notion_id' => 'xxxxssss-xxxxxx', 'release_date' => '2023-09-08']);
        Expansion::create(['attr' => 'XLN', 'name' => 'イクサラン', 'notion_id' => 'xxxxssss-xxxxxa', 'release_date' => '2017-11-17']);
        Expansion::create(['attr' => 'LCI', 'name' => 'イクサラン:失われし洞窟', 'notion_id' => 'xxxxssss-xxxxxb', 'release_date' => '2023-11-17']);
        Expansion::create(['attr' => 'MKM', 'name' => 'カルロフ邸殺人事件', 'notion_id' => 'xxxxssss-xxxxxc', 'release_date' => '2024-02-09']);
        Expansion::create(['attr' => 'SPG', 'name' => 'スペシャルゲスト', 'notion_id' => 'xxxxssss-xxxxxd', 'release_date' => '2023-02-09']);
        Expansion::create(['attr' => 'MH3', 'name' => 'モダンホライゾン3', 'notion_id' => 'xxxxssss-xxxxxe', 'release_date' => '2024-06-14']);
    }
}
