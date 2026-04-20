<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Residence;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'residence_id'   => Residence::factory(),
            'vendor_id'      => Vendor::factory(),
            'budget_line_id' => null,
            'title'          => fake()->sentence(4),
            'amount_total'   => fake()->randomFloat(2, 500, 50000),
            'status'         => InvoiceStatus::Draft,
        ];
    }

    public function validated(): static
    {
        return $this->state(['status' => InvoiceStatus::Validated]);
    }

    public function paid(): static
    {
        return $this->state(['status' => InvoiceStatus::Paid]);
    }
}
