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
        Schema::table('hr_candidates', function (Blueprint $table) {
            $table->enum('status', ['new', 'accepted', 'refused', 'shortlisted', 'accept_hr_interview', 'accept_technical_interview'])->default('new')->after('cv_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_candidates', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
