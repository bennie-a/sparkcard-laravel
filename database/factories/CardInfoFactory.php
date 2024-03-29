<?php

namespace Database\Factories;

use App\Models\CardInfo;
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
            'name' => $this->faker->realText(10),
            // 'en_name' => $this->random(10),
            'number' => $this->random(3),
            'image_url' => 'https://cards.scryfall.io/normal/front/d/3/'.$this->random(16),
        ];
    }

    private function random($size) {
        return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789"), 0, $size);
    }
}
