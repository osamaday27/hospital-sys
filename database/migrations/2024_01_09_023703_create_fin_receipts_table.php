<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatefinReceiptsTable extends Migration
{
    public function up()
    {
        Schema::create('fin_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_patient')->nullable();
			$table->string('id_reservation')->nullable();
            $table->string('invoices_number')->nullable();
            $table->string('invoices_id')->nullable();
            $table->string('pay_type')->nullable();
            $table->string('id_contract')->nullable();
            $table->string('total_paid')->nullable();
            $table->string('total_remainder')->nullable();
            $table->string('treasury')->nullable();
            $table->string('notes')->nullable();
            $table->string('branches')->nullable();
            $table->string('id_user')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('fin_receipts');
    }
}
