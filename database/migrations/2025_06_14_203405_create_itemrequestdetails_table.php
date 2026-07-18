<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRequestDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('item_request_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            // الربط بالجدول الرئيسي
            $table->foreignId('item_request_id')
                  ->constrained('item_requests')
                  ->onDelete('cascade');

            // الربط بجدول الأصناف (products)
            $table->foreignId('item_id')
                  ->constrained('products_data')
                  ->onDelete('cascade');

            // الحالة: pending, approved, rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            $table->text('notes')->nullable();  // ملاحظات

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_request_details');
    }
}
