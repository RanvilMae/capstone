<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plot;
use App\Models\User;

class PlotSeeder extends Seeder
{
    public function run()
    {
        // Ensure you have at least one user to assign these plots to
        $user = User::first() ?? User::factory()->create();

        $codes = [
            'P1V1T2', 'P2V1T3', 'P3V1T1', 'P4V6T2', 'P5V6T3', 'P6V6T1', 
            'P7V11T2', 'P8V11T3', 'P9V11T1', 'P10V12T1', 'P11V12T3', 'P12V12T2',
            'P13V7T1', 'P14V7T3', 'P15V7T2', 'P16V2T1', 'P17V2T3', 'P18V2T2',
            'P19V3T2', 'P20V3T3', 'P21V3T1', 'P22V8T2', 'P23V8T3', 'P24V8T1',
            'P25V13T2', 'P26V3T3', 'P27V13T1', 'P28V14T1', 'P29V14T3', 'P30V14T2',
            'P31V9T1', 'P32V9T3', 'P33V9T2', 'P34V4T1', 'P35V4T3', 'P36V4T2',
            'P37V5T2', 'P38V5T3', 'P39V5T1', 'P40V10T2', 'P41V10T3', 'P42V10T1',
            'P43V17T2', 'P44V17T3', 'P45V17T1', 'P46V16T2', 'P47T1'
        ];

        foreach ($codes as $code) {
            \App\Models\Plot::updateOrCreate(
                ['code' => $code], // This matches the code from your Excel image
                [
                    'user_id' => $user->id,
                    'farmer_id' => 1,
                    'plot_size_rai' => 1.0,
                    // By adding the code here, '1-Krabi Provincial - P1V1T2' 
                    // becomes unique and won't trigger the error
                    'plot_location' => "Krabi Provincial - $code", 
                ]
            );
        }
    }
}