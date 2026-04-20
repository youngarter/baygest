<?php

namespace Database\Factories;

use App\Models\Residence;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'residence_id' => Residence::factory(),
            'name'         => fake()->company(),
            'tax_id'       => fake()->optional()->numerify('########-#-###-###'),
        ];
    }
}
