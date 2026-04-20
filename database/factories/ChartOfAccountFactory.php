<?php

namespace Database\Factories;

use App\Enums\ChartOfAccountType;
use App\Models\ChartOfAccount;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChartOfAccount>
 */
class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition(): array
    {
        return [
            'residence_id' => Residence::factory(),
            'code'         => fake()->unique()->numerify('####'),
            'name'         => fake()->words(3, true),
            'type'         => fake()->randomElement(ChartOfAccountType::cases())->value,
        ];
    }

    public function asset(): static
    {
        return $this->state(['type' => ChartOfAccountType::Asset->value]);
    }

    public function expense(): static
    {
        return $this->state(['type' => ChartOfAccountType::Expense->value]);
    }

    public function liability(): static
    {
        return $this->state(['type' => ChartOfAccountType::Liability->value]);
    }
}
