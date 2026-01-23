<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_summaries', function (Blueprint $table) {
            $table->id(); // summary_id (PK)

            $table->foreignId('plot_id')
                ->constrained('plots')
                ->cascadeOnDelete();

            $table->foreignId('production_year_id')
                ->constrained('production_years')
                ->cascadeOnDelete();

            $table->decimal('dry_rubber_weight_kg', 12, 2); // น้ำหนักเนื้อยางแห้ง
            $table->decimal('total_amount_baht', 14, 2);   // ยอดเงิน (บาท)

            $table->timestamps();

            // Prevent duplicate summaries per plot per year
            $table->unique(['plot_id', 'production_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_summaries');
    }
};

