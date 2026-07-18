<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rights_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->boolean('rights_yes')->default(false);
            $table->boolean('rights_no')->default(false);
            $table->string('rights_action')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rights_assessments');
    }
};