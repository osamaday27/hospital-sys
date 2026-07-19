<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * تشغيل الميجريشن.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('status'); // إضافة عمود الملاحظات بعد حالة الحجز
        });
    }

    /**
     * التراجع عن الميجريشن.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
