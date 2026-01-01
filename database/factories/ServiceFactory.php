<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'service_name' => $this->faker->word(),
            'unit' => $this->faker->word(),
            'unit_price' => $this->faker->randomFloat(2, 100, 5000),
            'inserted_by' => 1,
        ];
    }
}
