<?php

namespace App\Exports;

use App\Models\LatexTransaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class FreshRubberSalesReportExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $type;
    protected $date;
    protected $month;
    protected $year;
    protected $quarter;
    protected $semester;

    public function __construct(array $filters = [])
    {
        $this->type = $filters['type'] ?? 'daily';
        $this->date = $filters['date'] ?? date('Y-m-d');
        $this->month = $filters['month'] ?? date('m');
        $this->year = $filters['year'] ?? date('Y');
        $this->quarter = $filters['quarter'] ?? 1;
        $this->semester = $filters['semester'] ?? 1;
    }

    public function view(): View
    {
        $query = LatexTransaction::with(['plot', 'plot.farmer']);

        // Filter based on period type
        switch ($this->type) {
            case 'monthly':
                $query->whereYear('transaction_date', $this->year)
                      ->whereMonth('transaction_date', $this->month);
                $periodLabel = Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
                break;

            case 'quarterly':
                $startMonth = ($this->quarter - 1) * 3 + 1;
                $endMonth = $startMonth + 2;
                $query->whereYear('transaction_date', $this->year)
                      ->whereBetween(\DB::raw('MONTH(transaction_date)'), [$startMonth, $endMonth]);
                $periodLabel = "Q{$this->quarter} {$this->year}";
                break;

            case 'semestral':
                $startMonth = $this->semester == 1 ? 1 : 7;
                $endMonth = $this->semester == 1 ? 6 : 12;
                $query->whereYear('transaction_date', $this->year)
                      ->whereBetween(\DB::raw('MONTH(transaction_date)'), [$startMonth, $endMonth]);
                $periodLabel = "Semester {$this->semester}, {$this->year}";
                break;

            case 'yearly':
                $query->whereYear('transaction_date', $this->year);
                $periodLabel = "Year {$this->year}";
                break;

            case 'daily':
            default:
                $query->whereDate('transaction_date', $this->date);
                $periodLabel = Carbon::parse($this->date)->format('l, F j, Y');
                break;
        }

        $transactions = $query->get();
        $pricePerKg = $transactions->avg('price_per_kg') ?? 39.50;

        return view('exports.fresh_rubber_report', [
            'transactions' => $transactions,
            'periodLabel' => $periodLabel,
            'pricePerKg' => $pricePerKg,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $highestRow = $sheet->getHighestRow();
        
        if ($highestRow >= 3) {
            $sheet->getStyle("A3:F{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            3 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            $highestRow => ['font' => ['bold' => true]],
        ];
    }
}