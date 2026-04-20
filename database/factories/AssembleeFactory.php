<?php

namespace Database\Factories;

use App\Models\Assemblee;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assemblee>
 */
class AssembleeFactory extends Factory
{
    protected $model = Assemblee::class;

    public function definition(): array
    {
        return [
            'residence_id'   => Residence::factory(),
            'annee_syndic'   => now()->year,
            'type'           => fake()->randomElement(['normal', 'extraordinaire']),
            'titre'          => 'AG ' . now()->year,
            'description'    => fake()->optional()->paragraph(),
            'date_assemblee' => fake()->dateTimeBetween('-1 year', '+6 months')->format('Y-m-d'),
        ];
    }
}
