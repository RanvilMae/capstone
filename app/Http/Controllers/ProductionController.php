<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LatexProductionImport;
use Illuminate\Support\Facades\Log;

class ProductionController extends Controller
{
    /**
     * Handle the Excel file upload for annual production data.
     * Designed for 3,303+ records per year.
     */
    public function uploadExcel(Request $request) 
{
    // Increase local execution time for 3,300+ rows
    set_time_limit(600);

    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv|max:128000'
    ]);

    try {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\LatexProductionImport, $request->file('excel_file'));
        
        return back()->with('success', 'Successfully integrated annual data (3,300+ records).');
    } catch (\Exception $e) {
        \Log::error('Upload Error: ' . $e->getMessage());
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}
}