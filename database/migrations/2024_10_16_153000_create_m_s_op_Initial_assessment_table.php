<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMSOpInitialAssessmentTable extends Migration
{
    public function up()
    {
        Schema::create('m_b_op_Initial_assessment', function (Blueprint $table) {
            $table->increments('id');
          

            $table->unsignedInteger('id_patient')->nullable(); // معرف المريض
            $table->foreign('id_patient')->references('id')->on('patient')->onDelete('set null');
            $table->unsignedInteger('id_visit')->nullable(); // معرف المريض
            $table->foreign('id_visit')->references('id')->on('patient_visits')->onDelete('set null');
      
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            
            $table->string('glasses_left')->nullable();
            $table->string('glasses_right')->nullable();

            $table->string('uava_bcva_left')->nullable();
            $table->string('uava_bcva_right')->nullable();

            $table->string('cycloplegic_refraction_left')->nullable();
            $table->string('cycloplegic_refraction_right')->nullable();

            $table->string('iop_left')->nullable();
            $table->string('iop_right')->nullable();

            $table->string('pupil_left')->nullable();
            $table->string('pupil_right')->nullable();

            $table->string('ocular_motility_left')->nullable();
            $table->string('ocular_motility_right')->nullable();

            $table->string('eyelid_and_lacrimal_left')->nullable();
            $table->string('eyelid_and_lacrimal_right')->nullable();

            $table->string('conjunctive_and_sclera_left')->nullable();
            $table->string('conjunctive_and_sclera_right')->nullable();
           
            $table->string('cornea_left')->nullable();
            $table->string('cornea_right')->nullable();
            
            $table->string('ac_and_iris_left')->nullable();
            $table->string('ac_and_iris_right')->nullable();
           
            $table->string('lens_left')->nullable();
            $table->string('lens_right')->nullable();

            $table->string('anterior_vitreous_left')->nullable();
            $table->string('anterior_vitreous_right')->nullable();

            $table->string('fundus')->nullable();
            $table->string('user_id')->nullable();
            $table->string('account_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('m_b_op_Initial_assessment');
    }
}
