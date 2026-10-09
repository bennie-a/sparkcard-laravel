<?php

namespace Database\Factories;

use App\Models\Expansion;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\CardConstant as CC;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expansion>
 */
class ExpansionFactory extends Factory
{
    protected $model = Expansion::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'notion_id' => Str::uuid(),
            GC::NAME => fake()->unique()->realText(10),
            CC::ATTR => Str::upper(fake()->lexify('???')),
            'release_date' => fake()->date()
        ];
    }
}
