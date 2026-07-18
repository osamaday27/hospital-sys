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
        Schema::create('adm_s_clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');  // الاسم باللغة العربية
            $table->string('name_en');  // الاسم باللغة الإنجليزية
            $table->foreignId('specialty_id')->constrained('specialties')->nullable();    // التخصص من جدول التخصصات
            $table->foreignId('floor_id')->constrained('adm_s_floors')->onDelete('cascade')->nullable();   // ارتباط بالدور
            $table->string('clinic_code')->unique()->nullable();    // كود العيادة
            $table->string('logo')->nullable();  // الشعار (يمكن أن يكون رابط صورة)
            $table->foreignId('service_id')->constrained('fin_services')->nullable();     // الخدمات من جدول الخدمات
            $table->enum('status', ['active', 'inactive'])->default('active');  // حالة العيادة
            $table->foreignId('asset_id')->constrained('asset_items')->nullable();    // الأصول من جدول الأصول
            $table->string('phone_number')->nullable();    // رقم الهاتف
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
            $table->foreignId('id_examination_service')->constrained('fin_services')->nullable();    // الأصول من جدول الأصول
            $table->foreignId('id_follow_up_service')->constrained('fin_services')->nullable();
            $table->foreignId('id_follow_up_opr_service')->constrained('fin_services')->nullable();
            $table->timestamps();
            $table->softDeletes();  // لحذف السجلات بشكل ناعم (اختياري)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_s_clinics');
    }
};