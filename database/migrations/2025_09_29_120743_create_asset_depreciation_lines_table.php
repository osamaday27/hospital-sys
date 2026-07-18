<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('asset_depreciation_lines', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('run_id')->index();
      $table->unsignedBigInteger('asset_id')->index();

      // سنأخذ Snapshot من إعدادات الأصل وقت التشغيل
      $table->enum('dep_method', ['SL','DDB','UOP'])->nullable();
      $table->integer('useful_life_months')->nullable();
      $table->decimal('dep_rate', 6, 3)->nullable(); // كنسبة سنوية أو شهرية حسب الطريقة
      $table->decimal('residual_value', 18, 2)->default(0);
      $table->decimal('cost_basis', 18, 2)->default(0);
      $table->integer('qty')->default(1);

      // وحدات الإنتاج (عند UOP)
      $table->integer('uop_estimated_total')->nullable();
      $table->integer('uop_units_period')->nullable();

      // مبالغ
      $table->decimal('amount', 18, 2)->default(0);             // إهلاك الشهر
      $table->decimal('accum_before', 18, 2)->default(0);
      $table->decimal('accum_after', 18, 2)->default(0);
      $table->decimal('nbv_before', 18, 2)->default(0);
      $table->decimal('nbv_after', 18, 2)->default(0);

      // لقطات إضافية
      $table->unsignedBigInteger('location_id')->nullable()->index();
      $table->unsignedBigInteger('branch_id')->nullable()->index();
      $table->date('in_service_date')->nullable();

      $table->text('remarks')->nullable();

      // تتبع
      $table->unsignedBigInteger('created_by')->nullable()->index();

      $table->softDeletes();
      $table->timestamps();

      $table->foreign('run_id')->references('id')->on('asset_depreciation_runs')->cascadeOnDelete();
      $table->foreign('asset_id')->references('id')->on('asset_items')->cascadeOnDelete();
      $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    });
  }

  public function down(): void {
    Schema::dropIfExists('asset_depreciation_lines');
  }
};
