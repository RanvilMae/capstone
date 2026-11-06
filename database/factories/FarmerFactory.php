<?php

namespace Database\Factories;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmerFactory extends Factory
{
    protected $model = Farmer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'farm_location' => $this->faker->city(),
            'farm_size' => $this->faker->randomFloat(2, 1, 50),
            'notes' => $this->faker->sentence(),
        ];
    }
}