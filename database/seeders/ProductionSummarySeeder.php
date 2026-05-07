<?php

// database/seeders/ProductionSummarySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farmer;
use App\Models\Plot;
use App\Models\ProductionYear;
use App\Models\ProductionSummary;

class ProductionSummarySeeder extends Seeder
{
    public function run(): void
    {
        // Create FY 2568
        $year = ProductionYear::firstOrCreate(
            ['year_label' => '2568'],
            ['start_date' => '2024-10-01', 'end_date' => '2025-07-31']
        );

        // Example farmers + plots
        $farmersData = [
            ['name' => 'Farmer A', 'farm_location' => 'Krabi'],
            ['name' => 'Farmer B', 'farm_location' => 'Krabi'],
        ];

        foreach ($farmersData as $fData) {
            $farmer = Farmer::firstOrCreate($fData);

            // Example plots per farmer
            $plotsData = [
                ['plot_size_rai' => 36, 'plot_location' => 'Plot 36'],
                ['plot_size_rai' => 21, 'plot_location' => 'Plot 21'],
            ];

            foreach ($plotsData as $pData) {
                $plot = Plot::firstOrCreate(array_merge($pData, ['farmer_id' => $farmer->id]));

                // Seed production summary (your actual data here)
                ProductionSummary::updateOrCreate(
                    ['plot_id' => $plot->id, 'production_year_id' => $year->id],
                    [
                        'dry_rubber_weight_kg' => rand(5000, 7000), // replace with real data
                        'total_amount_baht' => rand(500000, 650000) // replace with real data
                    ]
                );
            }
        }
    }
}
