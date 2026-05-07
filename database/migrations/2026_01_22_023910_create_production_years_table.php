<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_years', function (Blueprint $table) {
            $table->id(); // production_year_id (PK)

            $table->string('year_label'); // e.g., "2568"
            $table->date('start_date');   // e.g., 2024-10-01
            $table->date('end_date');     // e.g., 2025-07-31

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_years');
    }
};

