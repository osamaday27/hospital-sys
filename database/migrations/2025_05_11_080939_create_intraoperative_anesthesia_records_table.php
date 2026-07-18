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
        Schema::create('intraoperative_anesthesia_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_booking_id')->constrained('adm_b_surgery_bookings')->onDelete('cascade');
            $table->boolean('changed_from_pre_anesthesia')->default(false);
            $table->string('gcs')->nullable();
            $table->string('pulse')->nullable();
            $table->string('bp')->nullable();
            $table->string('spo2')->nullable();
            $table->string('npo')->nullable();
            $table->time('pre_assessment_time')->nullable();
        
            $table->json('monitoring_data')->nullable(); // store all grid rows as JSON
            $table->json('remarks')->nullable(); // array of 12 remarks
            $table->string('fluid_in')->nullable();
            $table->string('fluid_out')->nullable();
            $table->string('urine')->nullable();
            $table->string('blood_loss')->nullable();
            $table->string('net_balance')->nullable();
            $table->enum('ventilation', ['Spont', 'IPPV'])->nullable();
            $table->string('vt')->nullable();
            $table->string('rr')->nullable();
        
            $table->string('anesthesia_technique')->nullable(); // general, regional, sedation, local
            $table->text('medications')->nullable(); // JSON: bolus/increments/total
        
            $table->json('airway')->nullable();
            $table->json('monitor')->nullable();
            $table->json('iv_cannula')->nullable();
        
            $table->boolean('unusual_event')->default(false);
            $table->string('post_op_condition')->nullable();
            $table->string('discharge_to')->nullable();
            $table->string('bp_post')->nullable();
            $table->string('hr_post')->nullable();
            $table->string('rr_post')->nullable();
        
            $table->string('anesthesiologist_signature')->nullable();
            $table->timestamp('signature_datetime')->nullable();
        
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
        Schema::dropIfExists('intraoperative_anesthesia_records');
    }
};