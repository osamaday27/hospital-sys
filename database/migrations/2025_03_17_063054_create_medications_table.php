<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 🔐 إلزام قاعدة البيانات بتعطيل العلاقات مؤقتاً لتمرير بناء الجدول بدون أخطاء الترتيب
        Schema::disableForeignKeyConstraints();

        Schema::create('medications', function (Blueprint $table) {
            $table->id(); // هذا ينشئ تلقائياً Unsigned Big Integer
            $table->string('name'); // اسم الدواء
            $table->string('code')->unique(); // كود الدواء
            $table->text('description')->nullable(); // وصف الدواء
            $table->timestamps();
        });

        // 🔐 إعادة تفعيل فحص العلاقات بعد الحفظ بأمان
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('medications');
        Schema::enableForeignKeyConstraints();
    }
};
