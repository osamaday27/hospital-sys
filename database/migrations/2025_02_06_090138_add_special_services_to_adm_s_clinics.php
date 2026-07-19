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
        Schema::table('adm_s_clinics', function (Blueprint $table) {
            $table->foreignId('id_spe_examination_service')->nullable()->constrained('fin_services'); 
            $table->foreignId('id_spe_follow_up_service')->nullable()->constrained('fin_services'); 
            $table->foreignId('id_spe_follow_up_opr_service')->nullable()->constrained('fin_services'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adm_s_clinics', function (Blueprint $table) {
            $table->dropForeign(['id_spe_examination_service']);
            $table->dropForeign(['id_spe_follow_up_service']);
            $table->dropForeign(['id_spe_follow_up_opr_service']);

            $table->dropColumn([
                'id_spe_examination_service', 
                'id_spe_follow_up_service', 
                'id_spe_follow_up_opr_service'
            ]);
        });
    }
};
