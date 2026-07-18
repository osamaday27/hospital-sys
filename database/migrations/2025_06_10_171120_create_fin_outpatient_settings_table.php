<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fin_outpatient_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_account_id');       // الحساب المحاسبي للعيادات الخارجية
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('gl_account_id')
                  ->references('id')->on('fin_s_gl_accounts')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fin_outpatient_settings');
    }
};