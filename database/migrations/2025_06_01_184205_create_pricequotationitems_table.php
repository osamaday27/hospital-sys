<?php
// database/migrations/2025_06_01_000002_create_price_quotation_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceQuotationItemsTable extends Migration
{
    public function up()
    {
        Schema::create('price_quotation_items', function (Blueprint $table) {
            $table->id();

            // ربط بجدول عروض الأسعار
            $table->foreignId('price_quotation_id')
                  ->constrained('price_quotations')
                  ->onDelete('cascade');

            // ربط بجدول الأصناف
            $table->foreignId('item_id')
                  ->constrained('products_data')
                  ->onDelete('cascade');

            // ربط بجدول الوحدات
            $table->foreignId('unit_id')
                  ->constrained('product_units')
                  ->onDelete('cascade');

            // الكمية
            $table->decimal('quantity', 15, 2);

            // السعر (بدون ضريبة)
            $table->decimal('price', 15, 2);

            // قيمة الضريبة على هذا البند
            $table->decimal('tax_amount', 15, 2)->default(0);

            // ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_quotation_items');
    }
}
