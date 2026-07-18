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
       Schema::create('m_d_opth_angle_examinations', function (Blueprint $table) {
            $table->id();

            // معلومات أساسية
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            // علاقة بالطبيب
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');

            // بيانات العين اليمنى (أربع نقاط)
            $table->string('rt_shaffer_1')->nullable();
            $table->string('rt_pigmentation_1')->nullable();
            $table->string('rt_others_1')->nullable();
            $table->string('rt_comment_1')->nullable();

            $table->string('rt_shaffer_2')->nullable();
            $table->string('rt_pigmentation_2')->nullable();
            $table->string('rt_others_2')->nullable();
            $table->string('rt_comment_2')->nullable();

            $table->string('rt_shaffer_3')->nullable();
            $table->string('rt_pigmentation_3')->nullable();
            $table->string('rt_others_3')->nullable();
            $table->string('rt_comment_3')->nullable();

            $table->string('rt_shaffer_4')->nullable();
            $table->string('rt_pigmentation_4')->nullable();
            $table->string('rt_others_4')->nullable();
            $table->string('rt_comment_4')->nullable();

            // بيانات العين اليسرى (أربع نقاط)
            $table->string('lt_shaffer_1')->nullable();
            $table->string('lt_pigmentation_1')->nullable();
            $table->string('lt_others_1')->nullable();
            $table->string('lt_comment_1')->nullable();

            $table->string('lt_shaffer_2')->nullable();
            $table->string('lt_pigmentation_2')->nullable();
            $table->string('lt_others_2')->nullable();
            $table->string('lt_comment_2')->nullable();

            $table->string('lt_shaffer_3')->nullable();
            $table->string('lt_pigmentation_3')->nullable();
            $table->string('lt_others_3')->nullable();
            $table->string('lt_comment_3')->nullable();

            $table->string('lt_shaffer_4')->nullable();
            $table->string('lt_pigmentation_4')->nullable();
            $table->string('lt_others_4')->nullable();
            $table->string('lt_comment_4')->nullable();

            // مسار الصورة المجمعة
            $table->string('image_path')->nullable();
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
        Schema::dropIfExists('m_d_opth_angle_examinations');
    }
};