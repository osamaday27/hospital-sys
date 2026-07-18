<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_d_objective_refractions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_reservation');
            $table->unsignedBigInteger('id_visit');
            $table->string('type_visit');

            $table->string('refraction_type');
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

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_objective_refractions');
    }
};