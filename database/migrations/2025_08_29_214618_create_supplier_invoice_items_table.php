<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_invoice_items', function (Blueprint $table) {
            $table->id();

            // الفاتورة
            $table->unsignedBigInteger('invoice_id');

            // نوع البند
            $table->enum('item_type', ['product', 'service'])->default('product');

            // لو منتج
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_unit_id')->nullable();

            // لو خدمة
            $table->unsignedBigInteger('service_id')->nullable();

            // التفاصيل المالية
            $table->string('description')->nullable();    // وصف إضافي للبند
            $table->decimal('qty', 12, 2)->default(1);    // الكمية
            $table->decimal('unit_price', 15, 2)->default(0);  // سعر الوحدة
            $table->decimal('discount', 15, 2)->default(0);    // الخصم
            $table->decimal('tax', 15, 2)->default(0);         // الضريبة
            $table->decimal('total', 15, 2)->default(0);       // الإجمالي = (qty*unit_price - discount + tax)

            $table->timestamps();

            // علاقات
            $table->foreign('invoice_id')->references('id')->on('supplier_invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products_data')->onDelete('set null');
            $table->foreign('product_unit_id')->references('id')->on('product_units')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('suppliers_services')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_invoice_items');
    }
};
