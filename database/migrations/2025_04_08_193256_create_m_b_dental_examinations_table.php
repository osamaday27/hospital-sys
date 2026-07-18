<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_b_dental_examinations', function (Blueprint $table) {
            $table->id();

            // علاقات الزيارة والمريض
            $table->string('id_patient')->nullable();     // رقم المريض
            $table->string('id_visit')->nullable();       // رقم الزيارة
            $table->string('id_reservation')->nullable(); // رقم الحجز
            $table->string('type_visit')->nullable();     // نوع الزيارة

            // بيانات الفحص السريري
            $table->string('oral_hygiene')->nullable();             // نظافة الفم
            $table->string('gingival_condition')->nullable();      // حالة اللثة
            $table->string('plaque_index')->nullable();            // مؤشر البلاك
            $table->string('mobility')->nullable();                // حركة الأسنان
            $table->string('pocket_depth')->nullable();            // عمق الجيوب
            $table->string('bleeding_on_probing')->nullable();     // نزيف عند الفحص
            $table->string('calculus')->nullable();                // وجود جير
            $table->string('furcation_involvement')->nullable();   // تفرعات الجذور
            $table->string('recession')->nullable();               // انحسار اللثة
            $table->string('attachment_loss')->nullable();         // فقدان التثبيت
            $table->text('tooth_surface_status')->nullable();      // حالة أسطح الأسنان
            $table->text('occlusion_status')->nullable();          // الإطباق
            $table->text('tmd')->nullable();                       // اضطرابات المفصل الفكي
            $table->text('selected_teeth')->nullable();            // الأسنان المختارة
            // بيانات الإدارة
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_b_dental_examinations');
    }
};