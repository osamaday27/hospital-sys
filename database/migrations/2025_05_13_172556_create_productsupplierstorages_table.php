<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSupplierStoragesTable extends Migration
{
    public function up()
    {
        Schema::create('product_supplier_storages', function (Blueprint $table) {
            $table->id();

            // ربط الصنف
            $table->unsignedBigInteger('product_id');

            // مصفوفة IDs الموردين (json)
            $table->json('supplier_ids');

            // مصفوفة IDs ظروف التخزين (json)
            $table->json('storage_condition_ids');

            $table->timestamps();
            $table->softDeletes();

            // مفتاح أجنبي للصنف
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products_data')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_supplier_storages');
    }
}
