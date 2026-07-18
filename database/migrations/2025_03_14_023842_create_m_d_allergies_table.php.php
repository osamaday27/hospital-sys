<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('m_d_allergies', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient');
            $table->string('id_visit');
            $table->string('id_reservation');
            $table->string('type_visit');
            $table->string('drug_allergy')->nullable();
            $table->string('food_allergy')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_allergies');
    }
};