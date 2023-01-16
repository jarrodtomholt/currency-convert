<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRate>
 */
class ExchangeRateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'currency' => $this->faker->lexify('???'),
            'date' => now()->subDays(rand(0, 365)),
            'rate' => $this->faker->numerify('#.####'),
        ];
    }
}
