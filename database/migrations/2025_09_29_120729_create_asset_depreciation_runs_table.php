<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('asset_depreciation_runs', function (Blueprint $table) {
      $table->id();

      // أول يوم من الشهر الممثل للفترة (YYYY-MM-01)
      $table->date('period')->index();

      // حالات التشغيل
      $table->enum('status', ['draft','posted','void'])->default('draft')->index();

      // ملاحظات
      $table->text('notes')->nullable();

      // تتبع المستخدمين
      $table->unsignedBigInteger('created_by')->nullable()->index();
      $table->unsignedBigInteger('posted_by')->nullable()->index();
      $table->timestamp('posted_at')->nullable();

      $table->softDeletes();
      $table->timestamps();

      $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
      $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();

      $table->unique(['period','status'], 'uniq_period_status');
    });
  }

  public function down(): void {
    Schema::dropIfExists('asset_depreciation_runs');
  }
};
