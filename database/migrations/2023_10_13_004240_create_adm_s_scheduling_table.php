<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateadmsSchedulingTable extends Migration {

	public function up()
	{
		Schema::create('adm_s_scheduling', function(Blueprint $table) {
			$table->increments('id');
			$table->string('id_doctor')->nullable();
			$table->string('id_clinic')->nullable();
			$table->string('day')->nullable();
			$table->string('first_shift_from')->nullable();
			$table->string('first_shift_to')->nullable();
			$table->string('second_shift_from')->nullable();
			$table->string('second_shift_to')->nullable();
			$table->string('third_shift_from')->nullable();
			$table->string('third_shift_to')->nullable();
			$table->string('account_id')->nullable();
			$table->string('status')->nullable();
			$table->string('user_id')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('adm_s_scheduling');
	}
}
