<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Residence;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'residence_id'   => Residence::factory(),
            'unit_id'        => Unit::factory(),
            'amount'         => fake()->randomFloat(2, 100, 5000),
            'payment_method' => fake()->randomElement(['virement', 'cheque', 'especes']),
            'date_received'  => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
