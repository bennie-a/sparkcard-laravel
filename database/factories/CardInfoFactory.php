<?php

namespace Database\Factories;

use App\Models\CardInfo;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
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
            GlobalConstant::NAME => $this->faker->realText(10),
            CardConstant::EN_NAME => fake()->unique()->sentence(3),
            CardConstant::NUMBER => fake()->unique()->randomNumber(3),
            CardConstant::IMAGE_URL => fake()->url(),
            'color_id' => fake()->randomElement(['W', 'U', 'B', 'R', 'G', 'M', 'A']),
            CardConstant::PROMO_ID => 1
        ];
    }

    private function random($size) {
        return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789"), 0, $size);
    }
}
