<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->enum('case_type', ['doctor', 'hospital'])->default('hospital')->after('status');
        });
    }

    public function down()
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->dropColumn('case_type');
        });
    }
};