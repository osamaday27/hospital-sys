<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🔐 إلزام قاعدة البيانات بتعطيل العلاقات مؤقتاً لتمرير الحذف بدون أخطاء
        Schema::disableForeignKeyConstraints();

        // 🛠️ الخطوة 1: حذف قيد المفتاح الخارجي أولاً لفك ارتباط العمود
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            try {
                $table->dropForeign(['surgical_id']);
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا كان القيد غير موجود أو تم فكه مسبقاً
            }
        });

        // 🛠️ الخطوة 2: الآن يمكنك حذف العمود والـ Index الخاص به بأمان دون اعتراض من MySQL
        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('adm_b_surgery_bookings', 'surgical_id')) {
                $table->dropColumn('surgical_id');
            }
        });

        // 🔐 إعادة تفعيل فحص العلاقات بعد انتهاء الحفظ بنجاح
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('adm_b_surgery_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('adm_b_surgery_bookings', 'surgical_id')) {
                $table->unsignedBigInteger('surgical_id')->nullable();
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
