<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();

            // ربط ببطلب الاحتياج
            $table->foreignId('requisition_id')
                  ->constrained('purchase_requisitions')
                  ->onDelete('cascade');

            // ربط بجدول الأصناف (productdata → productdatas)
            $table->foreignId('item_id')
                  ->constrained('products_data')
                  ->onDelete('cascade');

            $table->integer('requested_qty');

            // ربط بجدول وحدات الأصناف (productunit → productunits)
            $table->foreignId('unit_id')
                  ->constrained('product_units')
                  ->onDelete('cascade');

            $table->decimal('pending_qty', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('other_stores_balance', 15, 2)->default(0);
            $table->decimal('last_price', 15, 2)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
}
