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
        Schema::create('m_d_eyeexamination', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            $table->string('section');
            $table->string('right_eye')->nullable();
            $table->string('left_eye')->nullable();
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('batch_id');
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
        Schema::dropIfExists('m_d_eyeexamination');
    }
};