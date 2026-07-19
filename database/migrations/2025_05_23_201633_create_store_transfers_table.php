<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('store_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->unsignedBigInteger('store_from_id');
            $table->unsignedBigInteger('store_to_id');
            $table->date('transfer_date');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('units_id');
            $table->decimal('quantity', 12, 2);
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('store_from_id')->references('id')->on('store_main_date');
            $table->foreign('store_to_id')->references('id')->on('store_main_date');
            $table->foreign('product_id')->references('id')->on('products_data');
            $table->foreign('units_id')->references('id')->on('units');
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_transfers');
    }
}
