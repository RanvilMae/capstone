<?php

namespace Database\Factories;

use App\Models\Farmer;
use App\Models\Plot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlotFactory extends Factory
{
    protected $model = Plot::class;

    public function definition(): array
    {
        return [
            'code' => 'PLT-' . $this->faker->unique()->randomNumber(5, true),
            'farmer_id' => Farmer::factory(),
            'user_id' => User::factory(),
            'plot_size_rai' => $this->faker->randomFloat(2, 1, 50),
            'plot_location' => $this->faker->city(),
            'notes' => $this->faker->sentence(),
        ];
    }
}