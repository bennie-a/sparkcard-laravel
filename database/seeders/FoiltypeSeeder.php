<?php

namespace Database\Seeders;

use App\Models\Foiltype;
use App\Models\Treatment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoiltypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Foiltype::create(['attr' => 'nonfoil', 'name' => '通常版']);
        Foiltype::create(['attr' => 'foil', 'name' => 'Foil']);
        Foiltype::create(['attr' => 'etched', 'name' => 'エッチングFoil']);
        Foiltype::create(['attr' => 'stepandcompleat', 'name' => 'S&C・Foil']);
        Foiltype::create(['attr' => 'textured', 'name' => 'テクスチャーFoil']);
        Foiltype::create(['attr' => 'confetti', 'name' => 'コンフェッティFoil']);
        Foiltype::create(['attr' => 'halofoil', 'name' => 'ハロー・Foil']);
        Foiltype::create(['attr' => 'oilslick', 'name' => 'オイリスリックFoil']);
    }
}
