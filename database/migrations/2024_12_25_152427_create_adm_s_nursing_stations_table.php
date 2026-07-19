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
        // 🔐 الحل الجذري: تعطيل فحص العلاقات مؤقتاً لتجنب أخطاء الترتيب الزمني للملّفات
        Schema::disableForeignKeyConstraints();

        Schema::create('adm_s_nursing_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // اسم المحطة بالعربي
            $table->string('name_en'); // اسم المحطة بالإنجليزي
            $table->foreignId('floor_id')->constrained('adm_s_floors')->onDelete('cascade'); // الدور
            $table->integer('room_count'); // عدد الغرف
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null'); // المدير
            $table
                ->enum('type', ['general', 'specialized', 'emergency'])
                ->default('general'); // النوع
            $table
                ->enum('status', ['active', 'inactive', 'under_maintenance'])
                ->default('active'); // الحالة
            $table->text('notes')->nullable(); // ملاحظات
            $table->unsignedBigInteger('created_by')->nullable(); // المستخدم الذي أنشأ السجل
            $table->timestamps();
            $table->softDeletes(); // الحذف الناعم
        });

        // 🔐 إعادة تفعيل فحص العلاقات فوراً بعد بناء الجدول بنجاح
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_s_nursing_stations');
    }
};
