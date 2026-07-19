<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fin_service_levels', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('hospital_code')->nullable();
            $table->text('level_type')->nullable();
			$table->string('status')->nullable();
            $table->string('default_branch')->nullable();
			$table->string('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fin_service_levels');
    }
};
