<?php

namespace Database\Seeders;

use App\Models\Expansion;
use App\Models\Stockpile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestExpansionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Expansion::factory()->createOne(['name' => '兄弟戦争', 'attr' => 'WAR']);
        Expansion::create(['attr' => 'BRO', 'name' => '兄弟戦争', 'notion_id' => 'd4d9832d-3bb3-4e07-be26-6a37ec198991', 'release_date' => '2022-11-08']);
        Expansion::create(['attr' => 'WAR', 'name' => '灯争大戦', 'notion_id' => '80a7660b-3de0-4ce1-8e51-1e90e123faae', 'release_date' => '2022-11-08']);
    }
}
