<?php

namespace Database\Factories;

use App\Enums\ResolutionType;
use App\Models\Assemblee;
use App\Models\Resolution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resolution>
 */
class ResolutionFactory extends Factory
{
    protected $model = Resolution::class;

    public function definition(): array
    {
        return [
            'assemblee_id'    => Assemblee::factory(),
            'title'           => fake()->sentence(4),
            'description'     => fake()->optional(0.7)->paragraph(),
            'resolution_type' => fake()->randomElement(ResolutionType::cases()),
        ];
    }
}
