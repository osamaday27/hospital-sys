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
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->string('reservation_source')
                  ->nullable()
                  ->after('case_type')
                  ->comment('الموديول اللي جه منه الحجز (outpatient, er, operation, lab, pharmacy, etc,callcenter)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->dropColumn('reservation_source');
        });
    }
};