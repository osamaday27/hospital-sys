<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    $assetsTable    = (new \App\models\assets\assetitem)->getTable();           // asset_items
    $suppliersTable = (new \App\Models\Suppliers\suppliersdata)->getTable();    // suppliers_data

    Schema::create('asset_maintenances', function (Blueprint $table) use ($assetsTable, $suppliersTable) {
      $table->id();

      $table->string('code')->unique(); // مثال: MT-2025-00001
      $table->unsignedBigInteger('asset_id');

      $table->enum('maintenance_type', ['preventive','corrective','inspection'])->default('corrective');
      $table->enum('status', ['open','in_progress','done','cancelled'])->default('open');
      $table->enum('priority', ['low','medium','high'])->default('medium');

      $table->date('reported_at');
      $table->date('started_at')->nullable();
      $table->date('finished_at')->nullable();

      $table->string('summary', 255);
      $table->text('description')->nullable();

      $table->unsignedBigInteger('vendor_id')->nullable();  // مزوّد خدمة خارجي (اختياري)
      $table->decimal('cost_total', 12, 2)->default(0);

      $table->date('next_due_date')->nullable();            // للوقائي لاحقًا
      $table->unsignedBigInteger('maintenance_request_id')->nullable(); // ربط لاحقًا بمنظومة الطلبات (بدون FK الآن)

      $table->string('attachment_path')->nullable();        // مرفق/تقرير (اختياري)
      $table->unsignedBigInteger('user_id')->nullable();    // مُنشئ السجل (اختياري)

      $table->timestamps();
      $table->softDeletes();

      $table->foreign('asset_id')->references('id')->on($assetsTable)->restrictOnDelete();
      $table->foreign('vendor_id')->references('id')->on($suppliersTable)->nullOnDelete();

      $table->index(['asset_id','status']);
      $table->index(['reported_at']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('asset_maintenances');
  }
};
