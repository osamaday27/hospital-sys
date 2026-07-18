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
        Schema::create('m_s_allergies_list', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الحساسية
            $table->string('specialization'); //التخصص
            $table->string('code')->unique(); // كود الحساسية
            $table->text('description')->nullable(); // وصف المرض
            $table->string('type'); // نوع الحساسية (مثل مادة أو دواء)
            $table->enum('severity', ['low', 'medium', 'high']); // درجة الخطورة
            $table->string('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_s_allergies_list');
    }
};