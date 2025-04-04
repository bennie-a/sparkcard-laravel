<?php

namespace Database\Seeders;

use App\Models\Expansion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpansionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expansion::create(['attr' => 'COM', 'name' => '共通エキスパンション',
             'notion_id' => 'c8f3a2b1-74e9-4d2f-b3a7-9e0f1c5d6a3b', 'release_date' => '1970-01-01']);
    }
}
