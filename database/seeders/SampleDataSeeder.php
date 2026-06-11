<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plot;
use App\Models\LatexTransaction;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin & Staff
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'is_approved' => true,
        ]);

        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff', // Matches your controller check
            'is_approved' => true,
        ]);

        // 2. Create Farmers
        // 2. Create Users and their Farmer Profiles
        $farmers = [];
        for ($i = 1; $i <= 5; $i++) {
            // A. Create the User record
            $user = User::create([
                'name' => "Farmer $i",
                'email' => "farmer$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'farmer',
                'is_approved' => true,
            ]);

            // B. Create the Farmer record (The missing link!)
            // We manually force the ID to match the User ID to keep it simple for your seeder
            \DB::table('farmers')->insert([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $farmers[] = $user;
        }

        // 3. Create Plots
        $plots = [];
        foreach ($farmers as $index => $farmer) {
            $plots[] = Plot::create([
                'farmer_id' => $farmer->id, // Now this ID exists in the 'farmers' table!
                'user_id' => $farmer->id,
                'plot_size_rai' => rand(10, 50),
                'plot_location' => 'Krabi Sector ' . ($index + 1),
                'notes' => 'Sample plot for ' . $farmer->name,
            ]);
        }
        // 4. Create Transactions for the last 6 months
        // 4. Create Transactions for the last 5 years
        $currentYear = date('Y');
        $startYear = $currentYear - 5;

        foreach ($plots as $plot) {
            for ($year = $startYear; $year <= $currentYear; $year++) {
                // Loop through each month
                for ($month = 1; $month <= 12; $month++) {

                    // Don't seed future months for the current year
                    if ($year == $currentYear && $month > date('n')) {
                        continue;
                    }

                    $monthDate = Carbon::create($year, $month, 1);

                    // Simulating 2-4 tappings per month
                    $tappingsCount = rand(2, 4);

                    for ($t = 0; $t < $tappingsCount; $t++) {
                        // Seasonal Yield Logic: Higher yields toward end of year
                        // Lower yields during "Wintering" (shedding leaves) Feb-March
                        $seasonFactor = match ($month) {
                            2, 3 => 0.6, // Low season
                            10, 11 => 1.3, // Peak season
                            default => 1.0,
                        };

                        $volume = rand(80, 150) * $seasonFactor;
                        $s1 = rand(30, 45);
                        $s2 = rand(30, 45);
                        $s3 = rand(30, 45);
                        $avgDrc = ($s1 + $s2 + $s3) / 3;

                        $price = 50 + ($year - $startYear) * 2; // Price increases slightly per year
                        $dryWeight = ($volume * $avgDrc) / 100;
                        $total = $dryWeight * $price;

                        LatexTransaction::create([
                            'plot_id' => $plot->id,
                            'user_id' => $plot->user_id,
                            'location' => $plot->plot_location,
                            'transaction_date' => $monthDate->copy()->addDays(rand(0, 27)),
                            'volume_kg' => $volume,
                            'dry_rubber_content' => $avgDrc,
                            'drc_sample_1' => $s1,
                            'drc_sample_2' => $s2,
                            'drc_sample_3' => $s3,
                            'dry_rubber_weight_kg' => $dryWeight,
                            'price_per_kg' => $price,
                            'total_amount' => $total,
                            // Optional: generate fake sample weights
                            'dry_sample_1' => ($volume * $s1) / 100,
                            'dry_sample_2' => ($volume * $s2) / 100,
                            'dry_sample_3' => ($volume * $s3) / 100,
                        ]);
                    }
                }
            }
        }


    }
}