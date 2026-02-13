<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate plots for the same farmer
            $table->unique(['farmer_id', 'plot_location'], 'farmer_plot_unique');
        });
    }

    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            // Drop the unique constraint if rolling back
            $table->dropUnique('farmer_plot_unique');
        });
    }
};