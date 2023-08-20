<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Treatment::create(['attr' => 'nonfoil', 'name' => '通常版']);
        Treatment::create(['attr' => 'foil', 'name' => 'Foil']);
        Treatment::create(['attr' => 'etched', 'name' => 'エッチングFoil']);
        Treatment::create(['attr' => 'stepandcompleat', 'name' => 'S&C・Foil']);
        Treatment::create(['attr' => 'textured', 'name' => 'テクスチャーFoil']);
        Treatment::create(['attr' => 'confetti', 'name' => 'コンフェッティFoil']);
        Treatment::create(['attr' => 'halofoil', 'name' => 'ハロー・Foil']);
        
    }
}
