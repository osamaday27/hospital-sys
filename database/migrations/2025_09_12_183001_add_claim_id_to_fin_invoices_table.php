<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_invoices', function (Blueprint $table) {
            // إضافة العمود
            $table->unsignedBigInteger('claim_id')->nullable()->after('reservation_id');

            // إضافة المفتاح الأجنبي
            $table->foreign('claim_id')
                ->references('id')
                ->on('fin_claims')
                ->nullOnDelete(); 
                // nullOnDelete = ON DELETE SET NULL
        });
    }

    public function down(): void
    {
        Schema::table('fin_invoices', function (Blueprint $table) {
            $table->dropForeign(['claim_id']);
            $table->dropColumn('claim_id');
        });
    }
};