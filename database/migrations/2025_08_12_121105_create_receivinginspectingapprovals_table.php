<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('receiving_inspecting_approvals', function (Blueprint $table) {
      $table->id();

      $table->foreignId('receiving_inspecting_id')
            ->constrained('receiving_inspecting')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

      $table->foreignIdFor(\App\Models\hr\Employee::class, 'employee_id')
            ->constrained()->cascadeOnUpdate()->restrictOnDelete();

      $table->boolean('is_chairman')->default(false);
      $table->string('responsibilities')->nullable();
      $table->enum('status', ['pending','approved','rejected'])->default('pending');
      $table->text('remarks')->nullable();

      $table->timestamps();
      $table->softDeletes();

      // ✅ أسماء قصيرة لتفادي طول المعرف
      $table->unique(['receiving_inspecting_id','employee_id'], 'ri_emp_uniq');
      $table->index(['receiving_inspecting_id','status','is_chairman'], 'ri_approvals_idx');
    });
  }

  public function down(): void {
    Schema::dropIfExists('receiving_inspecting_approvals');
  }
};
