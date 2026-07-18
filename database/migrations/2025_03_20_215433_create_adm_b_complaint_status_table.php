<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmBComplaintStatusTable extends Migration
{
    public function up()
    {
        Schema::create('adm_b_complaint_status', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            // responsible_id يتم تخزينه كـ json ليحتوي أكتر من قيمة (id)
            $table->json('responsible_id')->nullable();
            $table->unsignedBigInteger('complaint_id');
            $table->string('action')->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('complaint_id')
                  ->references('id')
                  ->on('adm_b_complaints')
                  ->onDelete('cascade');

            // updated_by مرتبط بجدول users
            $table->foreign('updated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_b_complaint_status');
    }
}
