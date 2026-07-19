<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatefinServicesVisitTable extends Migration
{
    public function up()
    {
        Schema::create('fin_services_visit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_invoices')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_services')->nullable();
            $table->string('id_patient')->nullable();
            $table->string('total_pricing')->nullable();
            $table->string('patient_contribution')->nullable();
            $table->string('contract_contribution')->nullable();
            $table->string('discount')->nullable();
            $table->string('quantity')->nullable();
            $table->string('currency')->nullable();			
            $table->string('notes')->nullable();
            $table->string('id_user')->nullable();
            $table->string('account_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('fin_services_visit');
    }
}
