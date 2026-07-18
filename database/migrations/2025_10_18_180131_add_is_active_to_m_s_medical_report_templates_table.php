<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{public function up()
{
    Schema::table('m_s_medical_report_templates', function (Blueprint $table) {
        $table->boolean('is_active')->default(1)->after('id');
    });
}

public function down()
{
    Schema::table('m_s_medical_report_templates', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}

};