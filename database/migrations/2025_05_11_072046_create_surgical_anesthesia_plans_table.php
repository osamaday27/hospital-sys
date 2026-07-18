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
        Schema::create('m_d_anaesthesia_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_booking_id')->constrained('adm_b_surgery_bookings')->onDelete('cascade');

            $table->integer('npo_hours')->nullable();
            $table->string('sedation_score')->nullable();
            $table->text('pending_investigations')->nullable();

            $table->json('anaesthesia_type')->nullable();
            $table->string('delivery_method')->nullable();
            $table->string('anaesthetic_name')->nullable();

            $table->string('fluid_management')->nullable();
            $table->json('blood_booking')->nullable();

            $table->boolean('premedication_none')->default(false);
            $table->json('premedication')->nullable();

            $table->text('special_monitoring')->nullable();
            $table->text('post_outcome')->nullable();
            $table->string('discharge_to')->nullable();

            $table->text('post_analgesia_plan')->nullable();

            $table->string('doctor_name')->nullable();
            $table->string('assistant_name')->nullable();
            $table->date('form_date')->nullable();
            $table->time('form_time')->nullable();

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
        Schema::dropIfExists('m_d_anaesthesia_plans');
    }
};