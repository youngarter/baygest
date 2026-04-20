<?php

namespace Database\Factories;

use App\Enums\VoteDecision;
use App\Models\Resolution;
use App\Models\Unit;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vote>
 */
class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        return [
            'resolution_id' => Resolution::factory(),
            'unit_id'       => Unit::factory(),
            'decision'      => fake()->randomElement(VoteDecision::cases()),
            'weight_used'   => fake()->randomFloat(4, 10, 1000),
        ];
    }
}
