<?php

namespace Tests\Database\Seeders\CLI\Normalize;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Illuminate\Database\Seeder;

class NormCardInfoSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mkm = 'MKM';
        $types = Promotype::findBySetCode($mkm);
        $mkm = Expansion::findBySetCode($mkm);

        foreach ($types as $type) {
            $name = fake()->unique()->realText(10);
            if ($type->attr !== 'draft') {
                $name .= "≪{$type->name}≫";
            }
            CardInfo::factory()->createOne([
                GlobalConstant::NAME => $name,
                CardConstant::EXP_ID => $mkm->notion_id,
                CardConstant::IS_FOIL => false,
                'foiltype_id' => 1, // Non-foil
                CardConstant::PROMO_ID => null
            ]);
        }
    }
}