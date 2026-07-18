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
        Schema::table('hr_penalties', function (Blueprint $table) {
            $table->enum('penalty_type', ['days', 'amount'])->default('amount')->after('reason');
            $table->integer('no_days')->nullable()->after('penalty_type');
            $table->decimal('amount', 10, 2)->nullable()->after('no_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_penalties', function (Blueprint $table) {
            $table->dropColumn(['penalty_type', 'no_days', 'amount']);
        });
    }
};
