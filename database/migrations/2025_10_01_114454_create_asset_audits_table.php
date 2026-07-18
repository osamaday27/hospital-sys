<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_audits', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();              // كود الجرد (AUD-...)
            $table->string('title')->nullable();                       // عنوان/وصف مختصر
            $table->enum('status', ['draft','in_progress','reconciled','void'])->default('draft');

            // نطاق الجرد (اختياري)
            $table->unsignedBigInteger('scope_location_id')->nullable();
            $table->unsignedBigInteger('scope_store_id')->nullable();

            // إحصاءات سريعة
            $table->unsignedInteger('expected_count')->default(0);     // أصول متوقعة ضمن النطاق عند بدء الجرد
            $table->unsignedInteger('scanned_count')->default(0);      // بنود تم مسحها
            $table->unsignedInteger('matched_count')->default(0);
            $table->unsignedInteger('mismatch_count')->default(0);
            $table->unsignedInteger('missing_count')->default(0);
            $table->unsignedInteger('extra_count')->default(0);

            $table->text('notes')->nullable();

            // Times & Users
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('started_by')->nullable();
            $table->unsignedBigInteger('finished_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audits');
    }
};
