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

        Schema::create('adm_s_beds', function (Blueprint $table) {
            $table->id();
            
            // ... هنا بقية حقول الجدول الخاصة بك (مثل الاسم، رقم السرير، إلخ) ...
            
            // حقل الربط المسبب للمشكلة متضمناً الإصلاح
            $table->foreignId('booking_id')
                  ->nullable()
                  ->constrained('adm_s_inpatient_booking')
                  ->onDelete('set null'); 

            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('adm_s_beds');
    }
};
