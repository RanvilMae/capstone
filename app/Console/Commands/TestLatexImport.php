<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\LatexProductionImport;
use Maatwebsite\Excel\Facades\Excel;

class TestLatexImport extends Command
{
    protected $signature = 'import:latex-test {file}';
    protected $description = 'Run LatexProductionImport and monitor validation failures';

    public function handle()
    {
        $filePath = $this->argument('file');
        $this->info("==========================================================================");
        $this->info("EXEC: Processing '{$filePath}' with LatexProductionImport");
        $this->info("==========================================================================");

        $import = new LatexProductionImport();
        
        try {
            Excel::import($import, storage_path("app/{$filePath}"));
            
            $failures = $import->failures();

            $this->warn("\n[VALIDATION REPORT]");
            $this->line("Total Validation Exceptions Caught: " . $failures->count());

            foreach ($failures as $failure) {
                $attribute = is_array($failure->attribute()) 
                    ? implode(', ', $failure->attribute()) 
                    : $failure->attribute();

                $this->error("\n------------------------------------------------------------------");
                $this->error(" -> Row Failure at Row #" . $failure->row());
                $this->error(" -> Attribute Failed : " . $attribute);
                $this->error(" -> Error Messages   : " . implode(', ', $failure->errors()));
                $this->error(" -> Given Values     : " . json_encode($failure->values()));
                $this->error("------------------------------------------------------------------");
            }

            $this->info("\n[SUMMARY]");
            $this->info(" - Import execution completed successfully with validation hooks active.");

        } catch (\Exception $e) {
            $this->error("Import process failed: " . $e->getMessage());
        }
    }

    
}