<?php

namespace Database\Factories;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Services\Constant\CardConstant as CC;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CardInfo>
 */
class CardInfoFactory extends Factory
{
    protected $model = CardInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'barcode' => $this->random(16),
            GC::NAME => $this->faker->realText(10),
            CC::EN_NAME => fake()->unique()->sentence(3),
            CC::NUMBER => fake()->unique()->randomNumber(3),
            CC::IMAGE_URL => fake()->url(),
            'color_id' => fake()->randomElement(['W', 'U', 'B', 'R', 'G', 'M', 'A', 'L', 'Land', 'T']),
            CC::PROMO_ID => 1,
            CC::IS_FOIL => false,
            CC::FOIL_ID => 1,
            CC::EXP_ID => Expansion::inRandomOrder()->first()->notion_id ?? Expansion::factory()->createOne()->notion_id
        ];
    }

    private function random(int $size) {
        return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789"), 0, $size);
    }
}
