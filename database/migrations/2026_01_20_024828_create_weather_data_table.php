<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('temperature', 5, 2)->nullable(); // °C
            $table->decimal('rainfall_mm', 6, 2)->nullable(); // mm
            $table->string('alert_message')->nullable();      // e.g., fungal risk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
