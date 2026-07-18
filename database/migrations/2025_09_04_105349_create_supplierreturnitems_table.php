<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_return_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supplier_return_id');     // FK للرأس
            $table->unsignedBigInteger('product_id');             // الصنف
            $table->unsignedBigInteger('units_id');               // الوحدة (من جدول units)
            $table->decimal('quantity', 18, 4);                   // الكمية

            // الأسعار والضريبة (للوحدة)
            $table->decimal('unit_price', 18, 4)->default(0);         // السعر قبل الضريبة
            $table->decimal('tax_value', 18, 4)->default(0);          // الضريبة (على الوحدة)
            $table->decimal('unit_price_with_tax', 18, 4)->default(0);// السعر شامل الضريبة (للوحدة)

            $table->string('batch_no', 100)->nullable();          // الباتش/التشغيلة
            $table->unsignedBigInteger('supplier_invoice_id')->nullable(); // ربط بفاتورة شراء (اختياري)
            $table->text('notes')->nullable();                    // ملاحظات

            $table->timestamps();
            $table->softDeletes();

            // العلاقات
            $table->foreign('supplier_return_id')
                  ->references('id')->on('supplier_returns')
                  ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('product_id')
                  ->references('id')->on('products_data')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('units_id')
                  ->references('id')->on('units')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('supplier_invoice_id')
                  ->references('id')->on('supplier_invoices')
                  ->nullOnDelete()->cascadeOnUpdate();

            // فهارس مساعدة
            $table->index(['product_id']);
            $table->index(['units_id']);
            $table->index(['batch_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_return_items');
    }
};
