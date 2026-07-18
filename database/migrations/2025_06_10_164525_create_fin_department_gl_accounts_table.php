<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fin_department_gl_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');   // مفتاح لقسم المستشفى
            $table->unsignedBigInteger('gl_account_id');   // مفتاح للحساب المحاسبي
            $table->enum('type', ['asset','liability','equity','income','expense'])->default('income');
            $table->timestamps();
            $table->softDeletes();

            // الفهارس والعلاقات
            $table->foreign('department_id')
                  ->references('id')->on('hr_sections')
                  ->onDelete('cascade');

            $table->foreign('gl_account_id')
                  ->references('id')->on('fin_s_gl_accounts')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fin_department_gl_accounts');
    }
};