<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('asset_maintenance_tasks', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('maintenance_id');
      $table->string('description');                 // وصف البند
      $table->date('performed_at')->nullable();      // تاريخ التنفيذ
      $table->decimal('hours', 8, 2)->default(0);    // ساعات العمل
      $table->decimal('cost', 12, 2)->default(0);    // تكلفة البند
      $table->string('technician_name')->nullable(); // اسم الفني
      $table->text('notes')->nullable();

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('maintenance_id')
            ->references('id')->on('asset_maintenances')
            ->cascadeOnDelete();

      $table->index(['maintenance_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('asset_maintenance_tasks');
  }
};
