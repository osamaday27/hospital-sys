<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_medical_sheet_templates', function (Blueprint $table) {
            $table->id();
            
            // 🧾 معلومات القالب الأساسية
            $table->string('template_name');
            $table->string('category')->nullable(); // Cataract, Glaucoma, Retina, Cornea
            $table->text('description')->nullable();
            $table->string('version')->default('1.0');
            
            // 🧠 بنية الشيت بالكامل (JSON Structure)
            $table->json('structure')->nullable();
            // structure = {
            //   "sections": [...],
            //   "narrative": "...",
            //   "is_printable": true,
            //   "is_public": false,
            //   "versions": [...]
            // }
            
            // 💡 تخصيص القالب
            $table->string('model_name')->nullable(); // Patient, Surgery, Doctor
            $table->unsignedBigInteger('speciality_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            
            // 🔢 ترتيب وعرض
            $table->integer('display_order')->default(0);
            $table->json('tags')->nullable(); // ["phaco", "IOL", "cataract"]
            
            // 🌍 الرؤية والحالة
            $table->enum('visibility', ['global', 'private', 'shared'])->default('global');
            $table->enum('status', ['active', 'inactive', 'draft', 'archived'])->default('active');
            
            // 📊 إحصائيات
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // 👤 المستخدم
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // 🔗 العلاقات
            $table->foreign('speciality_id')->references('id')->on('specialties')->nullOnDelete();
            $table->foreign('doctor_id')->references('id')->on('employees')->nullOnDelete();
            
            // 📇 فهارس للأداء
            $table->index('category');
            $table->index('status');
            $table->index('visibility');
            $table->index(['doctor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_medical_sheet_templates');
    }
};
