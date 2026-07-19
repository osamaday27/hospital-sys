<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_attendance', function (Blueprint $table) {
            $table->decimal('shortage', 5, 2)->nullable()->after('worked_hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_attendance', function (Blueprint $table) {
            $table->dropColumn('shortage');
        });
    }
}; 
