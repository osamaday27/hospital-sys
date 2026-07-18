<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('adm_s_answer_values', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->unsignedBigInteger('type_id'); // ربطه بجداول الأنواع
            $table->integer('value')->nullable()->default(null); // جعله عدد صحيح وقابل لـ null
            $table->timestamps();
            $table->softDeletes();


            // مفاتيح خارجية
            $table->foreign('type_id')->references('id')->on('adm_s_answer_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_s_answer_values');
    }
};
