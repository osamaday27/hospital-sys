<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('approvals', function (Blueprint $table) {
      $table->id();

      // سياق الاعتماد (نستخدمه للتوسّع لاحقًا)، مع ربطه بطلب التخلص
      $table->string('context', 50)->default('disposal');
      $table->unsignedBigInteger('request_id')->nullable();

      // مسار مستويات الاعتماد
      $table->unsignedTinyInteger('level')->default(1);
      $table->string('role_or_committee', 190)->nullable();

      // المعيّن (المخوَّل) لاعتماد هذا المستوى — اختياري
      $table->unsignedBigInteger('assignee_user_id')->nullable();
      $table->unsignedBigInteger('assignee_employee_id')->nullable();

      // من قام فعليًا بالاعتماد/الرفض
      $table->unsignedBigInteger('approver_user_id')->nullable();
      $table->unsignedBigInteger('approver_employee_id')->nullable();

      // قرار الاعتماد
      $table->enum('decision', ['pending','approved','rejected','returned'])->default('pending');
      $table->text('decision_notes')->nullable();
      $table->timestamp('decided_at')->nullable();

      // التتبع
      $table->unsignedBigInteger('created_by')->nullable();

      $table->softDeletes();
      $table->timestamps();

      // فهارس
      $table->index(['context','request_id','level']);
      $table->index(['assignee_user_id','assignee_employee_id']);
      $table->index(['approver_user_id','approver_employee_id']);
      $table->index('decision');

      // قيد فريد يمنع تكرار نفس المستوى لنفس الطلب (مع السماح بنسخة جديدة بعد حذف القديمة SoftDelete)
      $table->unique(['context','request_id','level','deleted_at'], 'approvals_unique_ctx_req_level');

      // علاقات
      $table->foreign('request_id')
            ->references('id')->on('disposal_requests')
            ->cascadeOnDelete();
    });
  }

  public function down(): void {
    Schema::dropIfExists('approvals');
  }
};
