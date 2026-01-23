<?php

// database/migrations/xxxx_create_latex_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('latex_transactions', function (Blueprint $table) {
            $table->id(); // transaction_id
            $table->foreignId('plot_id')->constrained('plots')->cascadeOnDelete();
            $table->date('transaction_date');
            $table->decimal('volume_kg', 12, 2);
            $table->decimal('dry_rubber_content', 5, 2); // %
            $table->decimal('price_per_kg', 12, 2);
            $table->decimal('total_amount', 14, 2);
            $table->foreignId('user_id')->constrained('users'); // Farmer who entered
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('latex_transactions');
    }
};

