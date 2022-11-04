<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $item = Language::create([
            'name'=>'日本語'
        ]);
        $item = Language::create([
            'name'=>'英語'
        ]);
        $item = Language::create([
            'name'=>'イタリア語'
        ]);
        $item = Language::create([
            'name'=>'簡体中国語'
        ]);
        $item = Language::create([
            'name'=>'繁体中国語'
        ]);

    }
}
