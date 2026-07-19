<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_d_examinations_nursing', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id')->nullable(); // معرف المريض
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('set null');
            $table->unsignedInteger('visit_id')->nullable(); // معرف المريض
            $table->foreign('visit_id')->references('id')->on('patient_visits')->onDelete('set null');
            $table->unsignedBigInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            // المستخدم الطالب
            $table->boolean('living_alone')->default(0);
            $table->boolean('living_with_family')->default(0);
            $table->boolean('living_with_others')->default(0);
            $table->string('living_other')->nullable();
            $table->boolean('special_needs_none')->default(0);
            $table->boolean('hearing')->default(0);
            $table->boolean('speech')->default(0);
            $table->boolean('language_barrier')->default(0);
            $table->string('other_needs')->nullable();
            $table->boolean('single')->default(0);
            $table->boolean('married')->default(0);
            $table->integer('number_of_children')->nullable();
            $table->boolean('smoking_no')->default(0);
            $table->boolean('smoking_yes')->default(0);
            $table->boolean('smoking_previous')->default(0);
            $table->integer('cigarettes_per_day')->nullable();
            $table->boolean('anxiety')->default(0);
            $table->boolean('anger')->default(0);
            $table->boolean('depression')->default(0);
            $table->boolean('insured')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_examinations_nursing');
    }
};
