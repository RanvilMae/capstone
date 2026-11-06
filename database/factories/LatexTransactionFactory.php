<?php

namespace Database\Factories;

use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LatexTransactionFactory extends Factory
{
    protected $model = LatexTransaction::class;

    public function definition(): array
    {
        $volumeKg = $this->faker->randomFloat(2, 50, 300);
        $drc = $this->faker->randomFloat(2, 25, 40);
        $dryWeight = round($volumeKg * ($drc / 100), 2);
        $price = $this->faker->randomFloat(2, 40, 70);

        return [
            'plot_id' => Plot::factory(),
            'user_id' => User::factory(),
            'location' => $this->faker->city(),
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'volume_kg' => $volumeKg,
            'dry_rubber_content' => $drc,
            'drc_sample_1' => $this->faker->randomFloat(2, 25, 40),
            'drc_sample_2' => $this->faker->randomFloat(2, 25, 40),
            'drc_sample_3' => $this->faker->randomFloat(2, 25, 40),
            'dry_sample_1' => $this->faker->randomFloat(2, 10, 30),
            'dry_sample_2' => $this->faker->randomFloat(2, 10, 30),
            'dry_sample_3' => $this->faker->randomFloat(2, 10, 30),
            'dry_rubber_weight_kg' => $dryWeight,
            'price_per_kg' => $price,
            'total_amount' => round($dryWeight * $price, 2),
        ];
    }
}