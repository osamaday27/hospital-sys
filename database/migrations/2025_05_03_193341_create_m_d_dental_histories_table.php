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
        Schema::create('m_d_dental_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('id_reservation');
            $table->unsignedBigInteger('id_visit');
            $table->string('type_visit')->nullable();

            $table->string('dental_care')->nullable();
            $table->json('past_treatment')->nullable();
            $table->text('jaw_trauma_notes')->nullable();
            $table->text('adverse_reaction_notes')->nullable();
            $table->text('dental_fears_notes')->nullable();
            $table->string('oral_health_state')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_dental_histories');
    }
};