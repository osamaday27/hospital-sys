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
        Schema::create('hr_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->text('offer_text');
            $table->unsignedBigInteger('job_id');
            $table->decimal('salary', 10, 2);
            $table->enum('status', ['new', 'approved', 'rejected'])->default('new');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('candidate_id')->references('id')->on('hr_candidates')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('hr_jobs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_offers');
    }
};
