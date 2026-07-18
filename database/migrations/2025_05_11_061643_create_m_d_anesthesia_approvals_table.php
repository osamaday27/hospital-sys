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
        Schema::create('m_d_anesthesia_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_booking_id');
            $table->string('doctor_name')->nullable();
            $table->date('approval_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('approval_file')->nullable(); // مسار الملف
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        
            $table->foreign('surgery_booking_id')->references('id')->on('adm_b_surgery_bookings')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_anesthesia_approvals');
    }
};