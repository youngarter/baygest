<?php

namespace Database\Factories;

use App\Models\Residence;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitType>
 */
class UnitTypeFactory extends Factory
{
    protected $model = UnitType::class;

    public function definition(): array
    {
        return [
            'residence_id'       => Residence::factory(),
            'name'               => fake()->randomElement(['Appartement', 'Magasin', 'Studio', 'Local commercial', 'Garage']),
            'default_annual_fee' => fake()->randomElement([2300.00, 4000.00, 5000.00, 1200.00]),
            'description'        => fake()->optional()->sentence(),
        ];
    }
}
