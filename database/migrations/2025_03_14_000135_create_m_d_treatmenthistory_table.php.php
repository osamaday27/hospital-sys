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
        Schema::create('m_d_treatment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient');
            $table->string('id_visit');
            $table->string('id_reservation');
            $table->string('type_visit');
            $table->string('drug');
            $table->string('eye');
            $table->integer('forNumber')->nullable();
            $table->string('period');
            $table->string('dose');
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
        Schema::dropIfExists('m_d_treatment_histories');
    }
};