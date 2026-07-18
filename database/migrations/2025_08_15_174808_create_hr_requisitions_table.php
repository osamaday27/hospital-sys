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
        Schema::create('hr_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_name');
            $table->unsignedBigInteger('job_id');
            $table->integer('number_of_candidates');
            $table->integer('required_experience_years')->nullable();
            $table->text('specifications')->nullable();
            $table->enum('status', ['pending', 'approved', 'refused'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('hr_jobs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_requisitions');
    }
};
