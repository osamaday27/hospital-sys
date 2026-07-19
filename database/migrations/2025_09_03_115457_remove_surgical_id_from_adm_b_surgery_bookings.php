<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
        if (Schema::hasColumn('adm_b_surgery_bookings', 'surgical_id')) {
            $table->dropColumn('surgical_id');
        }
    });
}

public function down()
{
    Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
        $table->unsignedBigInteger('surgical_id')->nullable();
    });
}

};
