<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractTable extends Migration {

	public function up()
	{
		Schema::create('contract', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('id_company')->nullable();
			$table->string('id_pricing')->nullable();
			$table->string('discount')->nullable();
			$table->string('discount_type')->nullable();
			$table->string('patient_contribution')->nullable();
			$table->string('patient_contribution_type')->nullable();
			$table->string('status')->nullable();
			$table->string('account_id')->nullable();
			$table->string('user_id')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('contract');
	}
}
