<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adm_b_surgery_timeouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_booking_id')->constrained('adm_b_surgery_bookings')->onDelete('cascade');
            $table->string('procedure')->nullable();
            $table->string('pre_op_diagnosis')->nullable();
            $table->string('patient_identity')->nullable();
            $table->string('correct_side')->nullable();
            $table->string('correct_procedure')->nullable();
            $table->string('availability_of_implants')->nullable();
            $table->string('anesthesia_concerns')->nullable();
            $table->string('antibiotic_prophylaxis_given')->nullable();
            $table->string('essential_imaging_displayed')->nullable();
            $table->boolean('timeout_documented')->default(false);

            $table->string('assistant_surgeon')->nullable();
            $table->string('anesthesiologist')->nullable();
            $table->string('sterile_nurse')->nullable();

            $table->time('timeout_time')->nullable();
            $table->date('timeout_date')->nullable();
            $table->time('procedure_start_time')->nullable();
            $table->time('procedure_end_time')->nullable();

            $table->string('surgeon_signature')->nullable();
            $table->string('nurse_signature')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adm_b_surgery_timeouts');
    }
};
