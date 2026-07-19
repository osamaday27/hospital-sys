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
        Schema::create('m_d_eye_examinations_eom', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');

            // بيانات عضلات العين اليمنى
            $table->string('right_sr')->nullable();
            $table->string('right_io')->nullable();
            $table->string('right_ir')->nullable();
            $table->string('right_so')->nullable();
            $table->string('right_lr')->nullable();
            $table->string('right_mr')->nullable();

            // بيانات عضلات العين اليسرى
            $table->string('left_sr')->nullable();
            $table->string('left_io')->nullable();
            $table->string('left_ir')->nullable();
            $table->string('left_so')->nullable();
            $table->string('left_lr')->nullable();
            $table->string('left_mr')->nullable();

            // مسار أو اسم الصورة المجمعة
            $table->string('eom_image_path')->nullable();

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
        Schema::dropIfExists('m_d_eye_examinations_eom');
    }
};
