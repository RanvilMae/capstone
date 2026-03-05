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
            // Adding the code column after 'id'
            $table->string('code')->nullable()->after('id')->unique();
        });
    }

    public function down()
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
