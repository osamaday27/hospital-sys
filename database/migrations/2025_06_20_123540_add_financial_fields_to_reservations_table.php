<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinancialFieldsToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            // المبلغ الإجمالي للخدمة
            $table->decimal('total', 10, 2)->nullable()->after('reservation_type');
            // مساهمة المريض
            $table->decimal('patient_contribution', 10, 2)->nullable()->after('total');
            // مساهمة العقد
            $table->decimal('contract_contribution', 10, 2)->nullable()->after('patient_contribution');
            // الخصم إن وجد
            $table->decimal('discount', 10, 2)->default(0)->after('contract_contribution');
            // طريقة الدفع المختارة
            $table->string('payment_method')->nullable()->after('discount');
            // المبلغ المدفوع فعلياً
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_method');
            // المبلغ المتبقي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'total',
                'patient_contribution',
                'contract_contribution',
                'discount',
                'payment_method',
                'paid_amount',
            ]);
        });
    }
}