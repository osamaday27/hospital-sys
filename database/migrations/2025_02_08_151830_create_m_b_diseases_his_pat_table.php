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
        Schema::create('m_b_diseases_his_pat', function (Blueprint $table) {
            $table->id(); // معرف السجل
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->text('diseases')->nullable(); // الحساسية
            $table->string('status')->nullable();
            $table->string('eye')->nullable();
            $table->integer('for_number')->nullable();
            $table->string('for_period')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
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
        Schema::dropIfExists('m_b_diseases_his_pat');
    }
};