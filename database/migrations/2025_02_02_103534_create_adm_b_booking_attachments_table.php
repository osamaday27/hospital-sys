<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_b_booking_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('adm_s_inpatient_booking')->onDelete('cascade');
            $table->string('patient_id_front')->nullable();
            $table->string('patient_id_back')->nullable();
            $table->string('companion_id_front')->nullable();
            $table->string('companion_id_back')->nullable();
            $table->string('admission_order')->nullable();
            $table->string('medical_report')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('adm_b_booking_attachments');
    }
};
