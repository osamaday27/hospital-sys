<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_d_other_systemic_diseases', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient');
            $table->string('id_visit');
            $table->string('id_reservation');
            $table->string('type_visit');
            $table->string('disease');
            $table->integer('duration')->nullable();
            $table->string('duration_unit')->nullable();
            $table->text('remark')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_other_systemic_diseases');
    }
};
