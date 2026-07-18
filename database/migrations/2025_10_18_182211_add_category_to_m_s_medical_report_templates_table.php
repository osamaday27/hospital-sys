<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // جدول القوالب الرئيسي
        Schema::create('m_s_medical_report_templates', function (Blueprint $table) {
            $table->id();
            
            // المعلومات الأساسية
            $table->string('name');
            $table->text('content');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            
            // الحالة والخصائص
            $table->boolean('is_global')->default(false)->comment('قالب عام للجميع');
            $table->boolean('is_active')->default(true)->comment('حالة القالب');
            
            // العلاقات
            $table->foreignId('specialty_id')
                  ->nullable()
                  ->constrained('specialties')
                  ->nullOnDelete()
                  ->comment('التخصص الافتراضي');
            
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('المستخدم المالك');
            
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('من أنشأ القالب');
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('آخر من حدث القالب');
            
            $table->timestamps();
            $table->softDeletes();
            
            // الفهارس
            $table->index('name');
            $table->index('category');
            $table->index('is_active');
            $table->index('is_global');
            $table->index('specialty_id');
            $table->index('user_id');
            $table->index(['is_active', 'is_global']);
            $table->index('created_at');
        });

        // جدول علاقة القالب بالتخصصات المتعددة
        Schema::create('m_s_medical_report_template_specialty', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('template_id')
                  ->constrained('m_s_medical_report_templates')
                  ->cascadeOnDelete();
            
            $table->foreignId('specialty_id')
                  ->constrained('specialties')
                  ->cascadeOnDelete();
            
            $table->timestamps();
            
            // ✅ حل مشكلة الاسم الطويل بتحديد اسم الفهرس يدويًا
            $table->unique(['template_id', 'specialty_id'], 'reporttpl_specialty_unique');
            
            // الفهارس
            $table->index('template_id');
            $table->index('specialty_id');
        });

        // جدول علاقة القالب بالمستخدمين المسموح لهم
        Schema::create('m_s_medical_report_template_user', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('template_id')
                  ->constrained('m_s_medical_report_templates')
                  ->cascadeOnDelete();
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            $table->timestamps();
            
            // ✅ نفس الشيء هنا لتفادي الاسم الطويل
            $table->unique(['template_id', 'user_id'], 'reporttpl_user_unique');
            
            // الفهارس
            $table->index('template_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_s_medical_report_template_user');
        Schema::dropIfExists('m_s_medical_report_template_specialty');
        Schema::dropIfExists('m_s_medical_report_templates');
    }
};