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
            "daya" => fake()->randomNumber(nbDigits: 3, strict: false),
            "koneksi" => fake()->randomDigit() < 5 ? KoneksiEnum::BLE : KoneksiEnum::WIFI
        ];
    }
}
