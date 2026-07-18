<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderNotificationTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_order_notification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')
                  ->constrained('purchase_orders')
                  ->onDelete('cascade');
            $table->foreignId('supplier_notification_id')
                  ->constrained('supplier_notifications')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_notification');
    }
}
