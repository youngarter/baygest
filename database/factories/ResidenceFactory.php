<?php

namespace Database\Factories;

use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Residence>
 */
class ResidenceFactory extends Factory
{
    protected $model = Residence::class;

    public function definition(): array
    {
        $name = fake()->company().' Résidence';

        return [
            'uuid' => (string) Str::uuid(),
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(4),
            'name' => $name,
            'description' => fake()->optional()->paragraph(),
            'avatar' => null,
            'images' => null,
            'address' => fake()->address(),
        ];
    }
}
