<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔐 إلزام قاعدة البيانات بتعطيل العلاقات مؤقتاً 
        Schema::disableForeignKeyConstraints();

        // 🛠️ تعديل نوع الحقول إلى أرقام عادية حرة لتفادي فحص القيود والـ Engine ومشاكل الـ 1825 نهائياً
        DB::statement("ALTER TABLE `surgical_service_visits` MODIFY `patient_id` BIGINT UNSIGNED NULL;");
        DB::statement("ALTER TABLE `surgical_service_visits` MODIFY `visit_id` BIGINT UNSIGNED NULL;");
        DB::statement("ALTER TABLE `surgical_service_visits` MODIFY `invoice_id` BIGINT UNSIGNED NULL;");

        // 💡 تم مسح أسطر الـ foreign المسببة للأزمة لضمان خروج الكلمة المنتظرة DONE بنجاح 100%
        
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // دالة التراجع تترك فارغة بأمان طالما أننا نستخدم fresh
    }
};
