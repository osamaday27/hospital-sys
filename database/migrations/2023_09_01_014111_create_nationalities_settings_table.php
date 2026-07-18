<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNationalitiesSettingsTable extends Migration {

	public function up()
	{
		Schema::create('nationalities_settings', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('default');
			$table->unsignedBigInteger('id_country');
            $table->foreign('id_country')->references('id')->on('Countries')->onDelete('cascade');
			$table->string('status');
			$table->string('account_id')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('nationalities_settings');
	}
}