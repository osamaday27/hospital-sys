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
        Schema::table('hr_requisitions', function (Blueprint $table) {
            $table->decimal('salary', 10, 2)->nullable()->after('specifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_requisitions', function (Blueprint $table) {
            $table->dropColumn('salary');
        });
    }
};
