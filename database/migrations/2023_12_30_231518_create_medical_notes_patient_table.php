<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatemedicalNotesPatientTable extends Migration {

	public function up()
	{
		Schema::create('medical_notes_patient', function(Blueprint $table) {
			$table->increments('id');
		    $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
			$table->string('notes')->nullable();
			$table->string('user_id')->nullable();
			$table->string('date')->nullable();
			$table->string('status')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('medical_notes_patient');
	}
}
