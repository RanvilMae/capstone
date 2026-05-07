<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('latex_transactions', function (Blueprint $table) {
            $table->decimal('dry_sample_1', 10, 2)->nullable();
            $table->decimal('dry_sample_2', 10, 2)->nullable();
            $table->decimal('dry_sample_3', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('latex_transactions', function (Blueprint $table) {
            //
        });
    }
};
