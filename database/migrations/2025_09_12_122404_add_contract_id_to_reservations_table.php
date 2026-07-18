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
    Schema::table('adm_s_reservations', function (Blueprint $table) {
        $table->unsignedBigInteger('contract_id')->nullable()->after('contract');
        $table->foreign('contract_id')->references('id')->on('fin_contract_last_level')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('adm_s_reservations', function (Blueprint $table) {
        $table->dropForeign(['contract_id']);
        $table->dropColumn('contract_id');
    });
}

};