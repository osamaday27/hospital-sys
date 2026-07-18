<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            $table->boolean('data_confirmed')
                  ->default(false)
                  ->after('account_id')
                  ->comment('0 = غير مكتمل / 1 = مكتمل (لا يتم إصدار الرقم الطبي إلا إذا كان مكتمل)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            $table->dropColumn('data_confirmed');
        });
    }
};