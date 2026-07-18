<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_d_oldglasses', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient');
            $table->string('id_visit');
            $table->string('id_reservation');
            $table->string('type_visit');
            $table->string('od_sph')->nullable();
            $table->string('od_cyl')->nullable();
            $table->string('od_axis')->nullable();
            $table->string('od_near_add')->nullable();
            $table->string('od_bcva')->nullable();
            $table->text('od_comment')->nullable();
            $table->string('os_sph')->nullable();
            $table->string('os_cyl')->nullable();
            $table->string('os_axis')->nullable();
            $table->string('os_near_add')->nullable();
            $table->string('os_bcva')->nullable();
            $table->text('os_comment')->nullable();
            $table->integer('duration')->nullable();
            $table->string('duration_unit')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_oldglasses');
    }
};