<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('visit_id')->nullable()->after('patient_id');
            // إذا كان هناك جدول زيارات يمكن إضافة مفتاح أجنبي
            // $table->foreign('visit_id')->references('id')->on('visits')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->dropColumn('visit_id');
        });
    }
};