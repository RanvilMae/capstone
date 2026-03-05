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
        Schema::table('plots', function (Blueprint $table) {
            // 1. Drop the foreign key first (check your migration for the exact name)
            $table->dropForeign(['farmer_id']); 
            
            // 2. Now drop the unique index
            $table->dropUnique('farmer_plot_unique');
            
            // 3. Re-add the foreign key without the unique restriction
            $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plots', function (Blueprint $table) {
            //
        });
    }
};
