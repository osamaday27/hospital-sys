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
        Schema::create('adm_s_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // الاسم باللغة العربية
            $table->string('name_en'); // الاسم باللغة الإنجليزية
            $table->string('address')->nullable(); // عنوان المبنى
            $table->integer('floors_count'); // عدد الأدوار
            $table->string('latitude')->nullable(); // إحداثيات (خط العرض)
            $table->string('longitude')->nullable(); // إحداثيات (خط الطول)
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
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
        Schema::dropIfExists('adm_s_buildings');
    }
};
