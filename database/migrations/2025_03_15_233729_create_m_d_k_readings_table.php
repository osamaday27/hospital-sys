<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDKReadingsTable extends Migration
{
    public function up()
    {
        Schema::create('m_d_k_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_reservation');
            $table->unsignedBigInteger('id_visit');
            $table->string('type_visit');
            
            // ✅ حقول K-Readings للعين اليمنى OD
            $table->decimal('od_k1_number', 5, 2);
            $table->integer('od_k1_axis');
            $table->decimal('od_k2_number', 5, 2);
            $table->integer('od_k2_axis');
            $table->text('od_comment')->nullable();

            // ✅ حقول K-Readings للعين اليسرى OS
            $table->decimal('os_k1_number', 5, 2);
            $table->integer('os_k1_axis');
            $table->decimal('os_k2_number', 5, 2);
            $table->integer('os_k2_axis');
            $table->text('os_comment')->nullable();

            // ✅ المستخدم الذي قام بإنشاء السجل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_k_readings');
    }
}
