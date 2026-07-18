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
        Schema::create('fin_price_lists', function (Blueprint $table) { // تغيير اسم الجدول ليبدأ بـ fin_
            $table->id(); // معرف الجدول
            $table->json('name')->nullable(); // اسم اللائحة (يدعم الترجمة باستخدام JSON)
            $table->year('fiscal_year')->nullable(); // السنة المالية
            $table->json('branches')->nullable(); // الفروع المرتبطة (JSON لتخزين قائمة الفروع)
            $table->date('start_date')->nullable(); // تاريخ البداية
            $table->date('end_date')->nullable(); // تاريخ النهاية
            $table->string('list_type', 50)->nullable(); // نوع القائمة
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('currency')->nullable(); // معرف الفرع الافتراضي
            $table->unsignedBigInteger('default_branch')->nullable(); // معرف الفرع الافتراضي
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
            $table->timestamps(); // تاريخ الإنشاء والتحديث
            $table->softDeletes(); // حذف منطقي
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fin_price_lists'); // تحديث اسم الجدول في الحذف
    }
};