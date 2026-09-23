<?php

namespace App\Imports;

use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Models\Farmer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Symfony\Component\Process\Process;

class LatexProductionImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected ?int $defaultPlotId;

    // Cache instances in memory to eliminate duplicate SELECT/INSERT queries
    protected array $farmersCache = [];
    protected array $plotsCache = [];

    public function __construct(?int $defaultPlotId = null)
    {
        $this->defaultPlotId = $defaultPlotId;
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $lastDate = null;
        $lastLocation = null;
        $currentUserId = Auth::id() ?? 1;

        // Safely fetch or create default fallback farmer
        $defaultFarmer = $this->getOrCreateFarmer('Default Farmer', 'default_farmer@example.com');

        foreach ($rows as $row) {
            $rawArray = array_values($row->toArray());

            $isFarmerDirectFormat = isset($row['farmer']) || isset($row['net_weight']) || isset($row['net_weight_1']);

            if ($isFarmerDirectFormat) {
                // ====================================================
                // TEMPLATE 1: FARMER DIRECT FORMAT
                // ====================================================

                $dateVal = $row['date'] ?? $rawArray[0] ?? null;
                if (!empty($dateVal)) {
                    $lastDate = $this->transformDate($dateVal);
                }
                $transactionDate = $lastDate ?? now()->format('Y-m-d H:i:s');

                $farmerName = isset($row['farmer']) ? trim((string)$row['farmer']) : ($rawArray[1] ?? null);
                $volumeKg = $this->parseNumeric(
                    $row['net_weight_1'] ?? $row['net_weight'] ?? $rawArray[2] ?? $rawArray[3] ?? null
                );

                if (empty($farmerName) && ($volumeKg === null || $volumeKg <= 0)) {
                    continue;
                }

                // Match or Create Farmer with unique email generation
                if (!empty($farmerName)) {
                    $cleanEmailName = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '_', $farmerName)));
                    $uniqueHash = substr(md5($farmerName), 0, 6);
                    $email = ($cleanEmailName ?: 'farmer') . '_' . $uniqueHash . '@example.com';

                    $farmer = $this->getOrCreateFarmer($farmerName, $email);
                } else {
                    $farmer = $defaultFarmer;
                }

                // Match or Create Plot
                $plot = null;
                if ($this->defaultPlotId) {
                    $plot = Plot::find($this->defaultPlotId);
                }
                if (!$plot) {
                    $plotCode = 'PLOT-' . strtoupper(substr(md5($farmer->name), 0, 5));
                    $plot = $this->getOrCreatePlot($plotCode, $farmer->id, $currentUserId, 'Krabi Province');
                }

                $avgDrc = $this->parseNumeric($row['drc'] ?? $rawArray[3] ?? $rawArray[4] ?? null) ?? 0;
                $dryRubberWeightKg = $this->parseNumeric($row['dry_rubber_weight'] ?? $rawArray[5] ?? null);

                if (($dryRubberWeightKg === null || $dryRubberWeightKg <= 0) && $volumeKg > 0 && $avgDrc > 0) {
                    $dryRubberWeightKg = ($volumeKg * $avgDrc) / 100;
                }

                $totalAmount = $this->parseNumeric($row['amount'] ?? $rawArray[6] ?? null);
                $wage = $this->parseNumeric($row['wage'] ?? $rawArray[7] ?? null);
                $labor = isset($row['labor']) ? trim((string)$row['labor']) : ($rawArray[8] ?? null);
                $pricePerKg = ($dryRubberWeightKg > 0 && $totalAmount > 0) ? ($totalAmount / $dryRubberWeightKg) : 45.00;

                $predictionLabel = $this->runBertInference($plot->code ?? 'FARMER-DIRECT', $volumeKg ?? 0, $avgDrc);

                LatexTransaction::updateOrCreate(
                    [
                        'plot_id'          => $plot->id,
                        'transaction_date' => $transactionDate,
                    ],
                    [
                        'user_id'                => $plot->user_id ?? $currentUserId,
                        'location'               => $plot->plot_location ?? 'Krabi Province',
                        'volume_kg'              => $volumeKg ?? 0,
                        'dry_rubber_content'     => round($avgDrc, 2),
                        'dry_rubber_weight_kg'   => round($dryRubberWeightKg ?? 0, 2),
                        'price_per_kg'           => round($pricePerKg, 2),
                        'total_amount'           => round($totalAmount ?? (($dryRubberWeightKg ?? 0) * $pricePerKg), 2),
                        'wage'                   => $wage,
                        'labor_info'             => $labor,
                        'quality_classification' => $predictionLabel,
                    ]
                );

            } else {
                // ====================================================
                // TEMPLATE 2: STANDARD LOG FORMAT
                // ====================================================

                $dateVal = $row['date'] ?? $rawArray[0] ?? null;
                if (!empty($dateVal)) {
                    $lastDate = $this->transformDate($dateVal);
                }

                $locationVal = $row['location'] ?? $rawArray[1] ?? null;
                if (!empty($locationVal)) {
                    $lastLocation = trim((string)$locationVal);
                }

                $plotCode = isset($row['plot_code']) ? strtoupper(trim((string)$row['plot_code'])) : (isset($rawArray[2]) ? strtoupper(trim((string)$rawArray[2])) : null);
                $volumeKg = $this->parseNumeric($row['fresh_wt_kg'] ?? $rawArray[3] ?? null);

                if (empty($plotCode) || $volumeKg === null || $volumeKg <= 0) {
                    continue;
                }

                $farmerName = isset($row['farmer']) ? trim((string)$row['farmer']) : (isset($row['farmer_name']) ? trim((string)$row['farmer_name']) : null);
                if (!empty($farmerName)) {
                    $cleanEmailName = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '_', $farmerName)));
                    $uniqueHash = substr(md5($farmerName), 0, 6);
                    $email = ($cleanEmailName ?: 'farmer') . '_' . $uniqueHash . '@example.com';

                    $farmer = $this->getOrCreateFarmer($farmerName, $email);
                } else {
                    $farmer = $defaultFarmer;
                }

                $drc1 = $this->parseNumeric($row['drc'] ?? $rawArray[4] ?? null);
                $drc2 = $this->parseNumeric($row['drc_2'] ?? $rawArray[5] ?? null);
                $drc3 = $this->parseNumeric($row['drc_3'] ?? $rawArray[6] ?? null);

                $drcSamples = array_filter([$drc1, $drc2, $drc3], fn($v) => !is_null($v) && $v > 0);

                $dry1 = $this->parseNumeric($row['dry_weight'] ?? $rawArray[7] ?? null);
                $dry2 = $this->parseNumeric($row['dry_weight_2'] ?? $rawArray[8] ?? null);
                $dry3 = $this->parseNumeric($row['dry_weight_3'] ?? $rawArray[9] ?? null);

                $avgDrc = count($drcSamples) > 0 ? (array_sum($drcSamples) / count($drcSamples)) : ($drc1 ?? 0);
                $dryRubberWeightKg = ($volumeKg > 0 && $avgDrc > 0) ? (($volumeKg * $avgDrc) / 100) : ($dry1 ?? 0);
                $pricePerKg = 45.00;

                $locationName = $lastLocation ?? 'Krabi Province';
                $plot = $this->getOrCreatePlot($plotCode, $farmer->id, $currentUserId, $locationName);

                if ($plot->farmer_id === $defaultFarmer->id && $farmer->id !== $defaultFarmer->id) {
                    $plot->update(['farmer_id' => $farmer->id]);
                }

                $transactionDate = $lastDate ?? now()->format('Y-m-d H:i:s');
                $predictionLabel = $this->runBertInference($plotCode, $volumeKg, $avgDrc);

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
    }

    /**
     * Cache farmer records in memory and lookup by Name OR Email to avoid unique constraint key collisions.
     */
    private function getOrCreateFarmer(string $name, string $email): Farmer
    {
        if (isset($this->farmersCache[$name])) {
            return $this->farmersCache[$name];
        }

        $farmer = DB::transaction(function () use ($name, $email) {
            // 1. Check if farmer exists by name OR by email
            $existing = Farmer::where('name', $name)
                ->orWhere('email', $email)
                ->first();

            if ($existing) {
                return $existing;
            }

            // 2. Safely create if no conflict exists
            return Farmer::create([
                'name'  => $name,
                'email' => $email,
            ]);
        }, 5);

        $this->farmersCache[$name] = $farmer;
        return $farmer;
    }

    /**
     * Cache plot records in memory and auto-retry transactions on DB deadlocks
     */
    private function getOrCreatePlot(string $code, int $farmerId, int $userId, string $location): Plot
    {
        if (isset($this->plotsCache[$code])) {
            return $this->plotsCache[$code];
        }

        $plot = DB::transaction(function () use ($code, $farmerId, $userId, $location) {
            return Plot::firstOrCreate(
                ['code' => $code],
                [
                    'farmer_id'     => $farmerId,
                    'user_id'       => $userId,
                    'plot_location'  => $location,
                    'plot_size_rai' => 0.00,
                ]
            );
        }, 5);

        $this->plotsCache[$code] = $plot;
        return $plot;
    }

    private function parseNumeric($value): ?float
    {
        if (is_null($value)) {
            return null;
        }
        $str = trim((string)$value);
        if ($str === '' || $str === '-' || $str === '–') {
            return null;
        }
        return is_numeric($str) ? floatval($str) : null;
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

            $trimmed = trim((string)$value);

            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $trimmed)) {
                $datePart = explode(' ', $trimmed)[0];
                $parts = explode('/', $datePart);

                if ((int)$parts[0] > 12) {
                    return Carbon::createFromFormat('d/m/Y', $datePart)->format('Y-m-d H:i:s');
                }
                return Carbon::createFromFormat('n/j/Y', $datePart)->format('Y-m-d H:i:s');
            }

            return Carbon::parse($trimmed)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now()->format('Y-m-d H:i:s');
        }
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}