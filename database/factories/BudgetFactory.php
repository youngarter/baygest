<?php

namespace Database\Factories;

use App\Models\Assemblee;
use App\Models\Budget;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'residence_id'            => Residence::factory(),
            'assemblee_id'            => Assemblee::factory(),
            'titre'                   => 'Budget ' . now()->year,
            'description'             => fake()->optional()->sentence(),
            'type'                    => fake()->randomElement(['global_estimatif', 'exceptionnel_estimatif']),
            'budget_reel'             => fake()->randomFloat(2, 10000, 500000),
            'seuil_alerte_estimatif'  => fake()->optional()->randomFloat(2, 5000, 100000),
            'seuil_alerte_reel'       => fake()->optional()->randomFloat(2, 5000, 100000),
        ];
    }
}
