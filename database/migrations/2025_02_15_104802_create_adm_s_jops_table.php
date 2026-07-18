<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('adm_s_jops', function (Blueprint $table) {
            $table->id();
            $table->text('name_ar')->nullable();
            $table->text('name_en')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('user_add');
            $table->softDeletes(); // يدعم الحذف الناعم
            $table->timestamps();

            $table->foreign('user_add')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_s_jops');
    }
};