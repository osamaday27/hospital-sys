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
        Schema::create('m_s_examination_findings', function (Blueprint $table) {
            $table->id(); // معرف فريد
            $table->string('name'); // اسم نتيجة الفحص
            $table->string('specialization'); //التخصص
            $table->string('code')->unique(); // كود نتيجة الفحص
            $table->text('description')->nullable(); // وصف نتيجة الفحص
            $table->enum('type', ['physical', 'laboratory', 'imaging', 'other']); // نوع الفحص
            $table->enum('severity', ['mild', 'moderate', 'severe']);
            $table->string('status')->nullable(); // درجة شدة النتيجة   $table->string('status')->nullable();
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
        Schema::dropIfExists('m_s_examination_findings');
    }
};