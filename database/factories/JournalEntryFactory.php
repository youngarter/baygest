<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        return [
            'residence_id' => Residence::factory(),
            'date'         => fake()->dateTimeBetween('-1 year', 'now'),
            'reference'    => strtoupper(fake()->bothify('JRN-####-??')),
            'description'  => fake()->sentence(5),
            'source_type'  => null,
            'source_id'    => null,
            'posted_at'    => null,
        ];
    }

    public function posted(): static
    {
        return $this->state(['posted_at' => now()]);
    }
}
