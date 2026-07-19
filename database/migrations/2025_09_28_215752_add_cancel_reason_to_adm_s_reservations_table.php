<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('cancel_reason_id')->nullable()->after('status')->comment('السبب من جدول absence_reasons');
            $table->text('cancel_note')->nullable()->after('cancel_reason_id')->comment('ملاحظات إضافية عند الإلغاء');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adm_s_reservations', function (Blueprint $table) {
            //
        });
    }
};
