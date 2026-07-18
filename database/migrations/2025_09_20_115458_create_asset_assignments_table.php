<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();

            // ربط سجل الإرجاع بسجل الإسناد الأصلي (اختياري)
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->unsignedBigInteger('asset_id'); // FK -> asset_items.id

            // الجهة المسند إليها (مرن: موظف/قسم/مخزن/مورد/أخرى)
            $table->string('assignable_type', 60);            // employee | department | store | supplier | other
            $table->unsignedBigInteger('assignable_id')->nullable(); // لو مرجع لجدول فعلي
            $table->string('assignable_name', 190)->nullable();      // الاسم الظاهر (للعرض السريع)

            // التواريخ
            $table->date('assigned_at');              // تاريخ الإسناد
            $table->date('due_back_at')->nullable();  // تاريخ الاستحقاق للإرجاع (اختياري)
            $table->date('returned_at')->nullable();  // تاريخ الإرجاع الفعلي (يُستخدم في سجل الإرجاع الجديد)

            // الحالة: نحتفظ بها لتمييز نوع السجل (مسند/مُعاد)
            $table->enum('status', ['assigned','returned'])->default('assigned');

            // ملف تسليم/استلام (اختياري)
            $table->string('handover_path', 255)->nullable();

            $table->text('notes')->nullable();

            $table->softDeletes(); // SoftDelete
            $table->timestamps();

            // علاقات
            $table->foreign('asset_id')
                ->references('id')->on('asset_items')
                ->cascadeOnDelete();

            // نفس الجدول: parent_id -> id
            $table->foreign('parent_id')
                ->references('id')->on('asset_assignments')
                ->nullOnDelete();

            // فهارس لتحسين الاستعلامات (خاصة المفتوحة Open)
            $table->index(['asset_id','status','deleted_at']);
            $table->index(['assignable_type','assignable_id']);
            $table->index('parent_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('asset_assignments');
    }
};
