<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('latex_transactions', function (Blueprint $table) {
            $table->index('plot_id');
            $table->index('transaction_date');
            $table->index(['plot_id', 'transaction_date']);
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
