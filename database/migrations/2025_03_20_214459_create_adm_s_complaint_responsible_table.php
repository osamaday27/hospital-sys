<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmSComplaintResponsibleTable extends Migration
{
    public function up()
    {
        Schema::create('adm_s_complaint_responsible', function (Blueprint $table) {
            $table->id();
            $table->string('responsibles');
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_s_complaint_responsible');
    }
}
