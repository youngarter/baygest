<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetLine>
 */
class BudgetLineFactory extends Factory
{
    protected $model = BudgetLine::class;

    public function definition(): array
    {
        return [
            'budget_id'            => Budget::factory(),
            'chart_of_account_id'  => ChartOfAccount::factory()->expense(),
            'title'                => fake()->sentence(3),
            'amount_previsionnel'  => fake()->randomFloat(2, 500, 50000),
        ];
    }
}
