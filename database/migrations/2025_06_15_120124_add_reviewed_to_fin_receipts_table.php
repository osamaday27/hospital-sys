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
        Schema::table('fin_receipts', function (Blueprint $table) {
            // إضافة عمود reviewed لتحديد ما إذا تم تأكيد المراجعة
            $table->boolean('reviewed')->default(false)->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fin_receipts', function (Blueprint $table) {
            $table->dropColumn('reviewed');
        });
    }
};