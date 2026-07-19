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
        Schema::create('pre_anesthesia_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_booking_id')->constrained('adm_b_surgery_bookings')->onDelete('cascade');
        
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('diagnosis')->nullable();
            $table->string('surgeon_unit')->nullable();
            $table->string('proposed_operation')->nullable();
        
            $table->json('history')->nullable();
            $table->json('drug_therapy')->nullable();
            $table->json('physical_exam')->nullable();
            $table->json('investigations')->nullable();
            $table->text('investigations_comments')->nullable();
            $table->json('lab_works')->nullable();
        
            $table->string('asa')->nullable();
            $table->enum('consent', ['yes', 'no'])->nullable();
            $table->enum('fit_for_operation', ['yes', 'no'])->nullable();
            $table->string('fit_for_operation_specify')->nullable();
        
            $table->string('prepare_fresh_blood')->nullable();
            $table->string('prepare_rbc')->nullable();
            $table->string('prepare_ffp')->nullable();
            $table->string('prepare_platelets')->nullable();
        
            $table->text('please_give_1')->nullable();
            $table->text('please_give_2')->nullable();
            $table->text('anesthesia_plan')->nullable();
        
            $table->string('physician_signature')->nullable();
            $table->date('form_date')->nullable();
            $table->time('form_time')->nullable();
        
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        
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
        Schema::dropIfExists('pre_anesthesia_assessments');
    }
};
