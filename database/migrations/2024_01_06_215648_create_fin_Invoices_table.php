<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatefinInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('fin_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->nullable();
            $table->string('patient_id')->nullable();
            $table->string('reservation_id')->nullable();
            $table->enum('status', ['paid', 'unpaid', 'canceled','pending_cancellation','pending_discount','partial','discount_approved','discount_rejected'])->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('patient_contribution', 15, 2)->nullable();
            $table->decimal('contract_contribution', 15, 2)->nullable();
            $table->decimal('discount', 15, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('reservation_type')->nullable();
            $table->string('treasury')->nullable();
            $table->decimal('paid', 15, 2)->nullable();
            $table->decimal('residual', 15, 2)->nullable();
            $table->string('user_id')->nullable();
            $table->string('branch')->nullable();
            $table->boolean('is_cancel')->default(false);
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('fin_invoices');
    }
}
