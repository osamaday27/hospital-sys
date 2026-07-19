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
        Schema::table('m_d_eye_examinations_eom', function (Blueprint $table) {
           $table->string('uploaded_image_path')->nullable()->after('eom_image_path');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_d_eye_examinations_eom', function (Blueprint $table) {
            //
        });
    }
};
