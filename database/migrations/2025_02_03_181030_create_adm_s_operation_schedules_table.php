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
        Schema::create('adm_s_operation_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_room')->nullable();
            $table->string('day')->nullable();
            $table->time('start_time'); // وقت بداية العملية
            $table->time('end_time'); // وقت نهاية العملية
            $table->integer('duration'); // مدة العملية بالدقائق
            $table->string('branches_id')->nullable();
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
        Schema::dropIfExists('adm_s_operation_schedules');
    }
};
