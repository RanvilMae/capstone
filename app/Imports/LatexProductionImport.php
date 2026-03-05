<?php

namespace App\Imports;

use App\Models\LatexTransaction;
use App\Models\Plot;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LatexProductionImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
{
    // 1. Use the numeric index '3' for Plot Code based on your dd() output
    $plotCode = $row[3] ?? null;
    $plot = \App\Models\Plot::where('code', trim($plotCode))->first();
    
    if (!$plot) {
        \Log::warning("Skipping: Plot Code " . ($plotCode ?? 'NULL') . " not found in DB.");
        return null; 
    }

    // 2. Use indexes 4 and 'drc' for weights
    $freshWt = $row[4] ?? 0;
    $drc = $row['drc'] ?? 0;

    if (empty($freshWt) || $freshWt == 0) return null;

    // 3. Manual calculation since Excel formulas can't be imported as raw data
    $dryWeight = (float)$freshWt * ((float)$drc / 100);

    return new \App\Models\LatexTransaction([
        'plot_id'              => $plot->id,
        'user_id'              => $plot->user_id,
        'transaction_date'     => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]),
        'location'             => $row[1] ?? 'Krabi Provincial',
        'volume_kg'            => (float)$freshWt,
        'dry_rubber_content'   => (float)$drc,
        'dry_rubber_weight_kg' => $dryWeight,
        'price_per_kg'         => 45.00,
        'total_amount'         => $dryWeight * 45.00,
    ]);
}

    // Tells Laravel to look at Row 2 for the names
    public function headingRow(): int { return 2; }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}