<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // ربط الزيارة بحجز العملية (اختياري + عليه index)
            $table->unsignedBigInteger('reservation_id')->nullable()->after('patient_id');
            $table->index('reservation_id', 'patient_visits_reservation_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            $table->dropIndex('patient_visits_reservation_id_idx');
            $table->dropColumn('reservation_id');
        });
    }
};
