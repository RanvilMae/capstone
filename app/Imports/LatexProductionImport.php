<?php

namespace App\Imports;

use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Models\Farmer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Symfony\Component\Process\Process;

class LatexProductionImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function headingRow(): int
    {
        return 2; // Matches Row 2 headers in your Excel sheet
    }

    public function collection(Collection $rows)
    {
        $lastDate = null;
        $lastLocation = null;
        $currentUserId = Auth::id() ?? 1;

        // Ensure a valid Farmer record exists to satisfy foreign key constraints
        $farmer = Farmer::firstOrCreate(
            ['id' => 1],
            [
                'name'  => 'Default Farmer',
                'email' => 'default_farmer@example.com',
            ]
        );

        foreach ($rows as $row) {
            $rawArray = $row->toArray();

            // 1. Forward-fill date if merged/blank
            $dateVal = reset($rawArray);
            if (!empty($dateVal)) {
                $lastDate = $this->transformDate($dateVal);
            }

            // 2. Forward-fill location
            $locationVal = $row['location'] ?? null;
            if (!empty($locationVal)) {
                $lastLocation = trim($locationVal);
            }

            // 3. Extract Plot Code & Fresh Weight
            $plotCode = isset($row['plot_code']) ? strtoupper(trim($row['plot_code'])) : null;
            $volumeKg = $this->parseNumeric($row['fresh_wt_kg'] ?? null);

            // Skip completely empty rows where no plot or fresh weight exists
            if (empty($plotCode) || $volumeKg <= 0) {
                continue;
            }

            // 4. Clean sample DRC values
            $drc1 = $this->parseNumeric($row['drc'] ?? null);
            $drc2 = $this->parseNumeric($row['drc_2'] ?? null);
            $drc3 = $this->parseNumeric($row['drc_3'] ?? null);

            $drcSamples = array_filter([$drc1, $drc2, $drc3], fn($v) => !is_null($v) && $v > 0);

            // 5. Clean Dry Weight samples
            $dry1 = $this->parseNumeric($row['dry_weight'] ?? null);
            $dry2 = $this->parseNumeric($row['dry_weight_2'] ?? null);
            $dry3 = $this->parseNumeric($row['dry_weight_3'] ?? null);

            // 6. Calculate Averages
            $avgDrc = count($drcSamples) > 0 ? (array_sum($drcSamples) / count($drcSamples)) : 0;
            $dryRubberWeightKg = ($volumeKg * $avgDrc) / 100;
            $pricePerKg = 45.00;

            // 7. Define parameters using the guaranteed farmer ID
            $locationName = $lastLocation ?? 'Krabi Province';
            $farmerId = $farmer->id;

            // 8. Find or create Plot
            $plot = Plot::firstOrCreate(
                ['code' => $plotCode],
                [
                    'farmer_id'     => $farmerId,
                    'user_id'       => $currentUserId,
                    'plot_location'  => $locationName,
                    'plot_size_rai' => 0.00,
                ]
            );

            $transactionDate = $lastDate ?? now()->format('Y-m-d H:i:s');

            // 9. Run Python BERT Inference
            $predictionLabel = $this->runBertInference($plotCode, $volumeKg, $avgDrc);

            // 10. Save or Update Record (Prevents Duplicates)
            LatexTransaction::updateOrCreate(
                [
                    'plot_id'          => $plot->id,
                    'transaction_date' => $transactionDate,
                ],
                [
                    'user_id'                => $plot->user_id ?? $currentUserId,
                    'location'               => $locationName,
                    'volume_kg'              => $volumeKg,
                    'dry_rubber_content'     => round($avgDrc, 2),
                    'drc_sample_1'           => $drc1,
                    'drc_sample_2'           => $drc2,
                    'drc_sample_3'           => $drc3,
                    'dry_sample_1'           => $dry1,
                    'dry_sample_2'           => $dry2,
                    'dry_sample_3'           => $dry3,
                    'dry_rubber_weight_kg'   => round($dryRubberWeightKg, 2),
                    'price_per_kg'           => $pricePerKg,
                    'total_amount'           => round($dryRubberWeightKg * $pricePerKg, 2),
                    'quality_classification' => $predictionLabel,
                ]
            );
        }
    }

    private function parseNumeric($value): ?float
    {
        if (is_null($value) || $value === '-' || trim((string)$value) === '') {
            return null;
        }
        return is_numeric($value) ? floatval($value) : null;
    }

    private function runBertInference($plotCode, $volumeKg, $avgDrc): string
    {
        try {
            $process = new Process([
                'python',
                base_path('scripts/predict_yield.py'),
                $plotCode,
                (string)$volumeKg,
                (string)round($avgDrc, 2),
            ]);

            $process->run();

            if ($process->isSuccessful()) {
                $output = json_decode($process->getOutput(), true);
                return $output['prediction'] ?? 'Standard Yield - Normal Quality';
            }
        } catch (\Exception $e) {
            // Fallback rule
        }

        return 'Standard Yield - Normal Quality';
    }

    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
            }
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now()->format('Y-m-d H:i:s');
        }
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}