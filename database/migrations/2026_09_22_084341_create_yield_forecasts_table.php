<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yield_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained('plots')->onDelete('cascade');
            $table->date('target_harvest_date');
            $table->integer('scheduled_tapping_days')->default(1);
            $table->decimal('target_revenue_goal', 10, 2);
            $table->decimal('estimated_drc_percent', 5, 2)->default(32.00);
            $table->decimal('target_market_price', 8, 2);
            
            // Calculated forecast outputs
            $table->decimal('projected_raw_latex_kg', 10, 2);
            $table->decimal('projected_dry_rubber_kg', 10, 2);
            $table->decimal('projected_revenue', 10, 2);
            $table->decimal('goal_completion_rate', 5, 2);
            $table->text('advisory_insight')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yield_forecasts');
    }
};