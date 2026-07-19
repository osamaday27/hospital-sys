<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operation_invoice_details', function (Blueprint $table) {
            $table->id();

            // روابط أساسية
            $table->unsignedBigInteger('invoice_id');          // يرتبط بجدول invoices
            $table->unsignedBigInteger('reservation_id');      // الحجز المرتبط
            $table->unsignedBigInteger('surgery_id')->nullable(); // الخدمة (العملية) من fin_services إن أحببت

            // بنود الأتعاب/التكاليف
            $table->decimal('surgeon_fee', 12, 2)->default(0);
            $table->decimal('assistant_fee', 12, 2)->default(0);
            $table->decimal('anesthesia_fee', 12, 2)->default(0);
            $table->string('anesthesia_type')->nullable();
            $table->decimal('nursing_fee', 12, 2)->default(0);
            $table->decimal('operation_room_fee', 12, 2)->default(0);
            $table->decimal('additional_time_fee', 12, 2)->default(0);
            $table->decimal('supplies_cost', 12, 2)->default(0);
            $table->decimal('other_services', 12, 2)->default(0);

            // المجاميع
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);   // نسبة % (مثلاً 15)
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);

            // توزيع (اختياري) على المريض/التعاقد
            $table->decimal('patient_value', 12, 2)->default(0);
            $table->decimal('contract_value', 12, 2)->default(0);

            // عام
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id', 'reservation_id']);

            // علاقات مرجعية (عدّل أسماء الجداول إن لزم)
            // $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            // $table->foreign('reservation_id')->references('id')->on('reservations')->cascadeOnDelete();
            // $table->foreign('surgery_id')->references('id')->on('fin_services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_invoice_details');
    }
};
