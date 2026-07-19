<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_d_colorvision', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient')->index();
            $table->unsignedBigInteger('id_reservation')->nullable()->index();
            $table->unsignedBigInteger('id_visit')->nullable()->index();
            $table->string('type_visit')->nullable();
            $table->json('od_colors')->nullable();
            $table->json('os_colors')->nullable();
            $table->text('od_comment')->nullable();
            $table->text('os_comment')->nullable();
            $table->unsignedBigInteger('created_by')->index();
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
        Schema::dropIfExists('m_d_colorvision');
    }
};
