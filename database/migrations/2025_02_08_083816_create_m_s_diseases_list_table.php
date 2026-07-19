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
        Schema::create('m_s_diseases_list', function (Blueprint $table) {
            $table->id(); // معرف فريد
            $table->string('name'); // اسم المرض
            $table->string('specialization'); //التخصص
            $table->string('code')->unique(); // كود المرض
            $table->text('description')->nullable(); // وصف المرض
            $table->enum('type', ['chronic', 'acute', 'genetic', 'infectious']); // نوع المرض
            $table->enum('severity', ['mild', 'moderate', 'severe']); // درجة شدة المرض
            $table->string('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->timestamps(); // تاريخ الإنشاء والتحديث
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_s_diseases_list');
    }
};
