<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceQuotationsTable extends Migration
{
    public function up()
    {
        Schema::create('price_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();

            // ربط بطلب الاحتياج
            $table->foreignId('requisition_id')
                  ->constrained('purchase_requisitions')
                  ->onDelete('cascade');

            // تاريخ بداية عرض السعر وتاريخ نهايته
            $table->date('start_date');
            $table->date('end_date');

            // المورد
            $table->foreignId('supplier_id')
                  ->constrained('suppliers_data')
                  ->onDelete('restrict');

            // فترة التسليم بالأيام
            $table->integer('delivery_period')->nullable();

            // مكان التسليم
            $table->string('delivery_location')->nullable();

            // طريقة الدفع
            $table->string('payment_method')->nullable();

            // إجمالي الضرائب
            $table->decimal('total_tax', 15, 2)->default(0);

            // إجمالي العرض (قبل الضريبة)
            $table->decimal('total_offer', 15, 2)->default(0);

            // السعر النهائي شامل الضريبة
            $table->decimal('final_price_including_tax', 15, 2)->default(0);

            // شروط الدفع
            $table->text('payment_terms')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            // المستخدم المنشئ
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_quotations');
    }
}
