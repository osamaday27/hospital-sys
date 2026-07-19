<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operative_procedure_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('adm_b_surgery_bookings')->nullOnDelete();

            $table->string('preoperative_diagnosis')->nullable();
            $table->string('postoperative_diagnosis')->nullable();
            $table->string('surgeon_name')->nullable();
            $table->string('first_assistant')->nullable();
            $table->time('time_started')->nullable();
            $table->time('time_ended')->nullable();
            $table->string('anesthesiologist')->nullable();
            $table->string('anesthesia_type')->nullable();
            $table->string('anesthesia_other')->nullable();
            $table->text('procedures_done')->nullable();
            $table->longText('operation_details')->nullable();

            $table->integer('blood_loss_ml')->nullable();
            $table->integer('blood_transfused_ml')->nullable();
            $table->boolean('complication_occurred')->default(false);
            $table->longText('complication_details')->nullable();

            // Specimens
            $table->boolean('specimens_sent')->default(false);
            $table->string('specimen_no')->nullable();
            $table->string('specimen_origin')->nullable();
            $table->string('specimen_size_description')->nullable();
            $table->boolean('specimen_sent_to_lab')->default(false);
            $table->boolean('specimen_pathology')->default(false);
            $table->boolean('specimen_culture')->default(false);

            // Implantable Devices
            $table->boolean('implantable_devices')->default(false);
            $table->longText('implant_details')->nullable();

            $table->date('report_date')->nullable();
            $table->time('report_time')->nullable();
            $table->string('surgeon_signature')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operative_procedure_reports');
    }
};
