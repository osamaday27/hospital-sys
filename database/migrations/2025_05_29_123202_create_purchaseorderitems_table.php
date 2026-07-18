<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            // ربط بـ أمر الشراء
            $table->foreignId('order_id')
                  ->constrained('purchase_orders')
                  ->cascadeOnDelete();

            // الصنف والوحدة
            $table->foreignId('item_id')
                  ->constrained('products_data')
                  ->cascadeOnDelete();
            $table->foreignId('unit_id')
                  ->constrained('product_units')
                  ->cascadeOnDelete();

            // الكميات
            $table->decimal('requested_qty', 15, 2);
            $table->decimal('pending_qty',   15, 2)->default(0);

            // أرصدة المخزون (إن احتجت)
            $table->decimal('current_balance',    15, 2)->default(0);
            $table->decimal('other_stores_balance', 15, 2)->default(0);

            // السعر وقيمة الضريبة
            $table->decimal('last_price', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);

            // ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
