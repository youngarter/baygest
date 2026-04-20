<?php

namespace Database\Factories;

use App\Models\Residence;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'residence_id' => Residence::factory(),
            'unit_type_id' => UnitType::factory(),
            'user_id'      => null,
            'name'         => strtoupper(fake()->randomLetter()) . fake()->numberBetween(1, 99),
            'tantiemes'    => fake()->optional(0.8)->randomFloat(4, 10, 1000),
        ];
    }

    public function withTantiemes(float $value): static
    {
        return $this->state(['tantiemes' => $value]);
    }
}
