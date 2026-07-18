<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_s_medical_complaints', function (Blueprint $table) {
            $table->id(); // معرف فريد
            $table->string('name'); // اسم الشكوى
            $table->string('specialization'); //التخصص
            $table->string('code')->unique(); // كود الشكوى
            $table->text('description')->nullable(); // وصف الشكوى
            $table->enum('type', ['physical', 'psychological', 'other']); // نوع الشكوى
            $table->enum('severity', ['mild', 'moderate', 'severe']); // درجة شدة الشكوى
               $table->string('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->timestamps(); // تاريخ الإنشاء والتحديث
            $table->softDeletes(); // التفعيل اللين للحذف
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_s_medical_complaints');
    }
};