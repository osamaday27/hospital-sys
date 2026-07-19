<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fin_contract_last_level', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
            $table->text('name')->nullable(); //    الاسم بالعربي انجليزي
            $table->text('company_code')->nullable(); // كود الشركة
            $table->text('commercial_registration')->nullable(); // السجل التجاري
            $table->text('address')->nullable(); // العنوان
            $table->text('notes')->nullable(); // ملاحظات
            $table->integer('claim_due_period')->nullable(); // فترة استحقاق المطالبات (عدد ايام)
            $table->integer('send_period')->nullable(); // فترة الارسال (عدد ايام)
            $table->unsignedBigInteger('second_level_id')->nullable(); // قائمة الأسعار
            $table->date('contract_start_date')->nullable(); // تاريخ بداية التعامل
            $table->enum('entity_type', ['cash', 'credit','all'])->nullable();
             // نوع الجهة (نقدي - اجل)
            $table->timestamps();
            $table->softDeletes();
            // Add foreign key constraint
            $table->foreign('second_level_id')->references('id')->on('fin_contract_second_level')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fin_contract_last_level');
    }
};
