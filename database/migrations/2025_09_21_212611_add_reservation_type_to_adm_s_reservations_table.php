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
            // إضافة الفيلد الجديد
            $table->enum('case_type', ['hospital', 'doctor'])
                  ->default('hospital')
                  ->after('time_slot')
                  ->comment('hospital = حالة مستشفى, doctor = حالة طبيب');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->dropColumn('case_type');
        });
    }
};