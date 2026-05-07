<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('latex_transactions', function (Blueprint $table) {

            $table->string('location')->nullable()->after('plot_id');
            $table->string('recorder')->nullable()->after('location');

            $table->decimal('drc_sample_1', 5, 2)->nullable();
            $table->decimal('drc_sample_2', 5, 2)->nullable();
            $table->decimal('drc_sample_3', 5, 2)->nullable();

            $table->decimal('dry_rubber_weight_kg', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('latex_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'recorder',
                'drc_sample_1',
                'drc_sample_2',
                'drc_sample_3',
                'dry_rubber_weight_kg'
            ]);
        });
    }
};

