<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plots', function (Blueprint $table) {
            $table->id(); // plot_id (PK)

            $table->foreignId('farmer_id')
                ->constrained('farmers')
                ->cascadeOnDelete();

            $table->decimal('plot_size_rai', 8, 2); // e.g., 36, 21, 42 Rai
            $table->string('plot_location')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
