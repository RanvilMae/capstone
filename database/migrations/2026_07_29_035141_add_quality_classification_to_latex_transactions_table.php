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
            $table->string('quality_classification')->nullable()->after('total_amount');
        });
    }

    public function down()
    {
        Schema::table('latex_transactions', function (Blueprint $table) {
            $table->dropColumn('quality_classification');
        });
    }
};
