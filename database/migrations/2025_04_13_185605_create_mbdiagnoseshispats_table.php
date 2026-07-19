<?php

// ✅ Migration
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_d_diagnoseshispateye', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_reservation');
            $table->unsignedBigInteger('id_visit');
            $table->string('type_visit');
            $table->string('diagnosis');
            $table->string('eye', 10);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_diagnoseshispateye');
    }
};
