<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_operative_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('adm_b_surgery_bookings')->nullOnDelete();

        
            $table->string('surgery_procedure')->nullable();
            $table->string('transfer_to')->nullable();

            $table->string('positioning')->nullable();
            $table->string('positioning_other')->nullable();
            $table->string('mobility')->nullable();
            $table->string('mobility_other')->nullable();
            $table->string('feeding')->nullable();
            $table->string('feeding_other')->nullable();
            $table->boolean('required_monitoring')->default(false);

            $table->string('wound')->nullable();
            $table->string('urine_output')->nullable();

            $table->text('instructions_post_anesthesia')->nullable();
            $table->text('investigations')->nullable();

            $table->text('general_instructions')->nullable();
            $table->text('follow_up')->nullable();

            $table->string('doctor_name')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('post_operative_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('post_operative_plans')->cascadeOnDelete();
            $table->string('medication')->nullable();
            $table->string('dose')->nullable();
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
        });

        Schema::create('post_operative_fluids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('post_operative_plans')->cascadeOnDelete();
            $table->string('fluid_name')->nullable();
            $table->string('volume')->nullable();
            $table->string('additive_name')->nullable();
            $table->string('infusion_rate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_operative_fluids');
        Schema::dropIfExists('post_operative_medications');
        Schema::dropIfExists('post_operative_plans');
    }
};