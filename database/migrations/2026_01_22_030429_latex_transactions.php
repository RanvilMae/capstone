<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('latex_transactions', function (Blueprint $table) {
            $table->id(); // transaction_id

            // Relationships
            $table->foreignId('plot_id')->constrained('plots')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Farmer/Staff who entered

            // Core Data
            $table->string('location')->nullable();
            $table->date('transaction_date');
            $table->decimal('volume_kg', 12, 2);
            $table->decimal('dry_rubber_content', 5, 2); // Final Average %

            // Granular Samples (Research Data)
            $table->decimal('drc_sample_1', 5, 2)->nullable();
            $table->decimal('drc_sample_2', 5, 2)->nullable();
            $table->decimal('drc_sample_3', 5, 2)->nullable();

            $table->decimal('dry_sample_1', 8, 2)->nullable();
            $table->decimal('dry_sample_2', 8, 2)->nullable();
            $table->decimal('dry_sample_3', 8, 2)->nullable();

            // Financial & Calculated Data
            $table->decimal('dry_rubber_weight_kg', 12, 2);
            $table->decimal('price_per_kg', 12, 2);
            $table->decimal('total_amount', 14, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('latex_transactions');
    }
};