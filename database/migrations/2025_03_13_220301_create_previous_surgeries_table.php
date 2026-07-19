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
        Schema::create('m_b_previous_surgeries_history_opth', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->string('surgery');
            $table->string('eye');
            $table->integer('forNumber')->nullable();
            $table->string('period');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
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
        Schema::dropIfExists('m_b_previous_surgeries_history_opth');
    }
};
