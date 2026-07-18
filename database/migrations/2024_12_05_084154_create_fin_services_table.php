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
        Schema::create('fin_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sublevel_id')->constrained('fin_service_sublevels')->onDelete('cascade');
            $table->text('name')->nullable();
            $table->text('hospital_code')->nullable();
            $table->text('description')->nullable();
            $table->text('patient_type')->nullable();
            $table->text('age_restriction_from')->nullable();
            $table->text('age_restriction_to')->nullable();
            $table->text('repeat_duration')->nullable();
            $table->text('confidentiality')->nullable();
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
        Schema::dropIfExists('fin_services');
    }
};