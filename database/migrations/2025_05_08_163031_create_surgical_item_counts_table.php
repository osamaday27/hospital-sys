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
        Schema::create('m_d_surgical_item_counts', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('surgery_booking_id');
            $table->string('item_name');
            $table->integer('initial_count')->nullable();
            $table->integer('additional_count')->nullable();
            $table->integer('closure_count')->nullable();
            $table->integer('final_count')->nullable();
            $table->boolean('final_count_verified')->default(false);
            $table->text('notes')->nullable();
            $table->date('count_date')->nullable();
            $table->time('count_time')->nullable();
            $table->string('surgeon_signature')->nullable();
            $table->string('nurse_signature')->nullable();
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
        Schema::dropIfExists('m_d_surgical_item_counts');
    }
};