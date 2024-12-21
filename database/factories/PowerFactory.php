<?php

namespace Database\Factories;

use App\Enums\KoneksiEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Power>
 */
class PowerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "sent_at" => $this->faker->dateTimeBetween('-2 hour', '-1 hour')->format('Y-m-d H:i:s.u'),
            "location" => $this->faker->randomDigit() < 5 ? KoneksiEnum::LOKAL : KoneksiEnum::INTERNET,
            "created_at" => $this->faker->dateTimeBetween('-1 hour', 'now')->format('Y-m-d H:i:s.u'),
            "key_pressed" => $this->faker->randomElement(['A', 'B', 'C', 'D']),
        ];
    }
}
