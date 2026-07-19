<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 🔐 إلزام قاعدة البيانات بتعطيل العلاقات مؤقتاً لتمرير التعديل بدون أخطاء
        Schema::disableForeignKeyConstraints();

        Schema::table('prescription_items', function (Blueprint $table) {
            // 💡 تم إصلاح الحقول بإزالة أمر الترتيب (after) لتفادي أخطاء روابط العلاقات وموقعها
            $table->string('medication_text')->nullable();
            $table->string('unit_text')->nullable();
            $table->string('duration_text')->nullable();
            $table->string('taken_text')->nullable();
        });

        // 🔐 إعادة تفعيل فحص العلاقات بعد الحفظ
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn(['medication_text', 'unit_text', 'duration_text', 'taken_text']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
