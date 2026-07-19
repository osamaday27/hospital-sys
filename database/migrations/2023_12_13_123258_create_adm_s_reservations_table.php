<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateadmsReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('adm_s_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_patient')->nullable();
            $table->string('visit_id')->nullable();
            $table->string('id_doctor')->nullable();
            $table->string('clinic_id')->nullable();
            $table->string('date_visit')->nullable();
            $table->string('date_reservations')->nullable();
            $table->string('time_slot')->nullable();
            $table->string('reservation_type')->nullable();
            $table->string('q_number')->nullable();
            $table->string('pay')->nullable();
            $table->string('contract')->nullable();
            $table->string('remaining_amount')->nullable();
            $table->string('status')->nullable();
            $table->string('default_branch')->nullable();
            $table->string('reason_cancel_reservation')->nullable();
            $table->string('user_id')->nullable();
            $table->string('account_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('adm_s_reservations');
    }
}
