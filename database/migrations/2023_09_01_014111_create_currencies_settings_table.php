<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCurrenciesSettingsTable extends Migration {

	public function up()
	{
		Schema::create('currencies_settings', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('default');
			$table->unsignedBigInteger('id_country');
            $table->foreign('id_country')->references('id')->on('Countries')->onDelete('cascade');
			$table->string('status');
			$table->string('account_id')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('currencies_settings');
	}
}