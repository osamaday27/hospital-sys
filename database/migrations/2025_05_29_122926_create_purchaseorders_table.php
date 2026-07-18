<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // ربط بـ عرض السعر وطلب الاحتياج
            $table->foreignId('quotation_id')
                  ->constrained('price_quotations')
                  ->cascadeOnDelete();
            $table->foreignId('requisition_id')
                  ->constrained('purchase_requisitions')
                  ->cascadeOnDelete();

            $table->string('order_number')->unique();
            $table->date('order_date');

            // العملة
            $table->string('currency', 3);

            // فترة التسليم بالأيام
            $table->integer('delivery_period')->nullable();

            // المورد
            $table->foreignId('supplier_id')
                  ->constrained('suppliers_data')
                  ->restrictOnDelete();

            // أولوية الطلب
            $table->enum('priority', ['low','normal','high','urgent'])
                  ->default('normal');

            // معلومات إضافية
            $table->string('delivery_location')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('notes')->nullable();

            // مجاميع الحسابات
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_amount_including_tax', 15, 2)->default(0);

            // مستخدم الإنشاء
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
