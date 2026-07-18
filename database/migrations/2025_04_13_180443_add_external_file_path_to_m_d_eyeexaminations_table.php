<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalFilePathToMDEyeexaminationsTable extends Migration
{
    public function up()
    {
        Schema::table('m_d_eyeexamination', function (Blueprint $table) {
            $table->string('external_file_path')->nullable()->after('comment');
        });
    }

    public function down()
    {
        Schema::table('m_d_eyeexamination', function (Blueprint $table) {
            $table->dropColumn('external_file_path');
        });
    }
}