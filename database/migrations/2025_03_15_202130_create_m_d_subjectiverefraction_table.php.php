<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_d_subjective_refractions', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient');
            $table->string('id_visit');
            $table->string('id_reservation');
            $table->string('type_visit');
            $table->string('refraction_type')->default('subjective');

            // OD (Right Eye)
            $table->string('od_sph')->nullable();
            $table->string('od_cyl')->nullable();
            $table->string('od_axis')->nullable();
            $table->string('od_near_add')->nullable();
            $table->string('od_ucva')->nullable();
            $table->string('od_near_uava')->nullable();
            $table->string('od_bcva')->nullable();
            $table->string('od_near_bcva')->nullable();

            // OS (Left Eye)
            $table->string('os_sph')->nullable();
            $table->string('os_cyl')->nullable();
            $table->string('os_axis')->nullable();
            $table->string('os_near_add')->nullable();
            $table->string('os_ucva')->nullable();
            $table->string('os_near_uava')->nullable();
            $table->string('os_bcva')->nullable();
            $table->string('os_near_bcva')->nullable();

            // OU (Both Eyes)
            $table->string('ou_ucva')->nullable();
            $table->string('ou_near_uava')->nullable();
            $table->string('ou_bcva')->nullable();
            $table->string('ou_near_bcva')->nullable();

            // IPD & Comments
            $table->string('ipd')->nullable();
            $table->text('od_comment')->nullable();
            $table->text('os_comment')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_subjective_refractions');
    }
};