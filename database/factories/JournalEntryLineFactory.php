<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntryLine>
 */
class JournalEntryLineFactory extends Factory
{
    protected $model = JournalEntryLine::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 10000);

        return [
            'journal_entry_id'    => JournalEntry::factory(),
            'chart_of_account_id' => ChartOfAccount::factory(),
            'debit'               => $amount,
            'credit'              => 0,
            'unit_id'             => null,
        ];
    }

    public function credit(): static
    {
        $amount = fake()->randomFloat(2, 100, 10000);

        return $this->state(['debit' => 0, 'credit' => $amount]);
    }
}
