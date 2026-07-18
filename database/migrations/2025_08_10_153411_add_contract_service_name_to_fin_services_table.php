<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fin_services', function (Blueprint $table) {
            $table->string('contract_service_name_ar')->nullable()->after('name'); // اسم الخدمة عربي
            $table->string('contract_service_name_en')->nullable()->after('contract_service_name_ar'); // اسم الخدمة إنجليزي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fin_services', function (Blueprint $table) {
            $table->dropColumn(['contract_service_name_ar', 'contract_service_name_en']);
        });
    }
};