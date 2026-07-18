<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierOpeningBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_opening_balances', function (Blueprint $table) {
            $table->bigIncrements('id');

            // مفتاح المورد الخارجي
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers_data')
                  ->onDelete('cascade');

            // السنة
            $table->integer('year');

            // رصيد دائن ورصيد مدين
            $table->decimal('credit_balance', 15, 2)->default(0);
            $table->decimal('debit_balance',  15, 2)->default(0);

            // soft deletes
            $table->softDeletes();

            // created_at و updated_at
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_opening_balances');
    }
}
