<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discharge_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('adm_b_surgery_bookings')->nullOnDelete();

         

            $table->text('final_diagnosis')->nullable();
            $table->text('operation_done')->nullable();
            $table->text('significant_history')->nullable();
            $table->text('case_progression')->nullable();
            $table->enum('patient_condition', ['Cured', 'Improved', 'Stationary', 'Fair', 'Poor', 'DAMA', 'Transferred'])->nullable();

            // Diet
            $table->string('diet_instruction')->nullable();
            $table->string('diet_other')->nullable();

            // Implanted devices
            $table->boolean('implanted_device')->default(false);
            $table->text('implanted_device_instructions')->nullable();

            // Follow up
            $table->text('follow_up_activity')->nullable();
            $table->string('follow_up_visit')->nullable();
            $table->text('emergency_instructions')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Confirmation
            $table->boolean('copy_given')->default(false);

            // Signatures
            $table->string('doctor_name')->nullable();
            $table->string('doctor_signature')->nullable();
            $table->date('report_date')->nullable();
            $table->time('report_time')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('discharge_summary_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discharge_summary_id')->constrained('discharge_summaries')->cascadeOnDelete();
            $table->string('medication_name')->nullable();
            $table->string('dose_route')->nullable();
            $table->string('frequency')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discharge_summary_medications');
        Schema::dropIfExists('discharge_summaries');
    }
};