<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('m_d_pin_holes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            $table->string('od_scale')->nullable();
            $table->string('os_scale')->nullable();
            $table->text('od_comment')->nullable();
            $table->text('os_comment')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
            // إضافة العلاقات الخارجية
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_d_pin_holes');
    }
};
