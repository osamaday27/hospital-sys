<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_b_follow_assessment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_patient')->nullable(); // معرف المريض
            $table->foreign('id_patient')->references('id')->on('patient')->onDelete('set null');
            $table->unsignedInteger('id_visit')->nullable(); // معرف المريض
            $table->foreign('id_visit')->references('id')->on('patient_visits')->onDelete('set null');
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->string('ant_segment_left')->nullable();
            $table->string('ant_segment_right')->nullable();
            $table->string('uava_bcva_left')->nullable();
            $table->string('uava_bcva_right')->nullable();
            $table->string('iop_left')->nullable();
            $table->string('iop_right')->nullable();
            $table->string('fundus')->nullable();
            $table->string('user_id')->nullable();
            $table->string('account_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_b_follow_assessment');
    }
};