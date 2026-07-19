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
        Schema::table('surgical_service_visits', function (Blueprint $table) {
            // إضافة العمود الخاص بالمريض وربطه بجدول patient
            $table->unsignedInteger('patient_id')->nullable()->after('id');
            $table->unsignedInteger('visit_id')->nullable()->after('patient_id');
            $table->unsignedInteger('invoice_id')->nullable()->after('visit_id');
    

            $table->foreign('patient_id')
                ->references('id')
                ->on('patient')
                ->onDelete('cascade');
                
            $table->foreign('visit_id')
                ->references('id')
                ->on('patient_visits')
                ->onDelete('cascade');
                      
            $table->foreign('invoice_id')
                ->references('id')
                ->on('fin_invoices')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surgical_service_visits', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn('patient_id');
        });
    }
};
