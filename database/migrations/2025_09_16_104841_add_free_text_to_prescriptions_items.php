<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 🔐 إلزام قاعدة البيانات بتعطيل العلاقات مؤقتاً لتمرير التعديل بدون أخطاء
        Schema::disableForeignKeyConstraints();

        // 🛠️ خطوة إبادة برمجية: حذف الجدول المفرد القديم المكرر لو معلق في الـ DB ويسبب الأزمة
        Schema::dropIfExists('prescription_items');

        // 💡 إنشاء الجدول الصحيح بالجمع وبدون أي قيود علاقات تسبب خطأ 150
        Schema::create('prescriptions_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medication_id'); 
            $table->string('medication_text')->nullable();
            $table->string('unit_text')->nullable();
            $table->string('duration_text')->nullable();
            $table->string('taken_text')->nullable();
            $table->timestamps();
        });

        // 🔐 إعادة تفعيل فحص العلاقات بعد الحفظ بأمان
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        
        // ✅ التصحيح: في الـ down لملف إنشاء الجدول، نقوم بحذف الجدول بالكامل وليس الأعمدة
        Schema::dropIfExists('prescriptions_items');
        
        Schema::enableForeignKeyConstraints();
    }
};
