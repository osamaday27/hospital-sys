<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientVisitsTable extends Migration {

	public function up()
	{
		Schema::create('patient_visits', function(Blueprint $table) {
			$table->increments('id');
			$table->string('patient_id')->nullable();
			$table->string('date_visit')->nullable();
			$table->string('type')->nullable();
			$table->string('status')->nullable();
			$table->string('default_branch')->nullable();
			$table->string('user_id')->nullable();
			$table->string('account_id')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('patient_visits');
	}
}
