<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_audit_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('audit_id');
            $table->unsignedBigInteger('asset_id')->nullable();        // يسمح ببند "زيادة" (asset_id=null)
            $table->string('asset_code')->nullable();                  // كود/QR تم مسحه

            // الحالة المتوقعة وقت بدء الجرد
            $table->unsignedBigInteger('expected_location_id')->nullable();
            $table->unsignedBigInteger('expected_store_id')->nullable();
            $table->string('expected_status', 20)->nullable();         // in_service, maintenance, retired, scrapped

            // القيم الممسوحة (المعثور عليها)
            $table->unsignedBigInteger('found_location_id')->nullable();
            $table->unsignedBigInteger('found_store_id')->nullable();
            $table->string('found_status', 20)->nullable();

            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();

            $table->enum('result', ['matched','location_mismatch','store_mismatch','status_mismatch','missing','extra'])->default('missing');

            // اختيار المستخدم تطبيق التغيير على الأصل عند "التسوية"
            $table->boolean('apply_change')->default(false);

            // تتبّع
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->string('notes', 500)->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('audit_id')->references('id')->on('asset_audits')->onDelete('cascade');
            $table->index(['audit_id', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_lines');
    }
};
