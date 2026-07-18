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
        Schema::create('m_b_complaints_his_pat', function (Blueprint $table) {
            $table->id(); // معرف السجل
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->text('complaints')->nullable(); // الحساسية
            $table->text('eye')->nullable(); // الحساسية
            $table->text('onset')->nullable(); // الحساسية
            $table->text('duration')->nullable(); // الحساسية
            $table->text('course')->nullable(); // الحساسية
            $table->text('remarks')->nullable(); // الحساسية
            $table->string('status')->nullable();
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
        Schema::dropIfExists('m_b_complaints_his_pat');
    }
};