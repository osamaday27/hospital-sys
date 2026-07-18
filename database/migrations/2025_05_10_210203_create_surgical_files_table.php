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
        Schema::create('surgical_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_booking_id');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
    
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
        Schema::dropIfExists('surgical_files');
    }
};