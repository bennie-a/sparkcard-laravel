<?php

namespace Database\Factories;

use App\Models\Expansion;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'notion_id' => $this->random(),
            'base_id' => mt_rand(0, 999999)
        ];
    }

    private function random() {
        return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789"), 0, 36);
    }
}
