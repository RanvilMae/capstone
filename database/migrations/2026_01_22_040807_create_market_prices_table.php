<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('market_prices')) { 
            Schema::create('market_prices', function (Blueprint $table) {
                $table->id();
                $table->decimal('price_per_kg', 10, 2);
                $table->date('date');
                $table->string('source')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};
