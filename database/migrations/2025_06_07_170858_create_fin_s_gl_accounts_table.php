<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fin_s_gl_accounts', function (Blueprint $table) {
            $table->id();

            // معلومات الحساب
            $table->string('code')->unique();                // كود الحساب
            $table->string('name_ar');                       // الاسم بالعربية
            $table->string('name_en');                       // الاسم بالإنجليزية
            $table->text('description')->nullable();         // وصف للحساب

            // التصنيف
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']); 
            // نوع الحساب (أصل - التزام - حقوق ملكية - دخل - مصروف)

            $table->enum('category', [
                'cash', 'bank', 'customer', 'supplier', 'tax', 'other'
            ])->nullable(); // فئة فرعية (صندوق، بنك، عميل...)

            // علاقة بالحساب الرئيسي
            $table->foreignId('parent_id')->nullable()->constrained('fin_s_gl_accounts'); // حساب أب إن وجد
            $table->boolean('is_group')->default(false); // إذا كان هذا الحساب مجموعة فقط وليس حساب حقيقي

            // الرصيد
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->enum('balance_type', ['debit', 'credit'])->default('debit');

            // بيانات إضافية
            $table->boolean('status')->default(true); // مفعل أم لا
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fin_s_gl_accounts');
    }
};
