<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('receiving_inspecting_items', function (Blueprint $table) {
            $table->string('batch_no')->nullable()->after('rejection_reason');   // رقم التشغيل
            $table->date('expiry_date')->nullable()->after('batch_no');         // تاريخ الانتهاء
        });
    }

    public function down(): void {
        Schema::table('receiving_inspecting_items', function (Blueprint $table) {
            $table->dropColumn(['batch_no', 'expiry_date']);
        });
    }
};
