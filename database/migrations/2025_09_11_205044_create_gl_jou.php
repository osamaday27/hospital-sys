<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // باتش اليومية (تجميعة قيود)
        Schema::create('gl_journal_batches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('source', 50)->default('COSTING')->index(); // مصدر النظام
            $table->date('period_start')->nullable()->index();
            $table->date('period_end')->nullable()->index();
            $table->enum('status', ['draft','posted'])->default('draft')->index();
            $table->decimal('total_debit', 18, 6)->default(0);
            $table->decimal('total_credit', 18, 6)->default(0);
            $table->unsignedBigInteger('cost_batch_id')->nullable()->index(); // ربط بباتش التكلفة
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // قيد يومية (رأس القيد)
        Schema::create('gl_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id')->index();
            $table->string('reference', 100)->nullable();
            $table->date('entry_date')->index();
            $table->string('description')->nullable();
            $table->decimal('total_debit', 18, 6)->default(0);
            $table->decimal('total_credit', 18, 6)->default(0);
            $table->timestamps();

            $table->index(['batch_id','entry_date']);
        });

        // سطور القيد
        Schema::create('gl_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entry_id')->index();
            $table->unsignedInteger('line_no')->default(1);
            $table->string('account', 50)->index();
            $table->decimal('debit', 18, 6)->default(0);
            $table->decimal('credit', 18, 6)->default(0);
            $table->string('description')->nullable();

            // أبعاد تحليلية اختيارية
            $table->unsignedBigInteger('cost_center_id')->nullable()->index();
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->enum('case_type', ['opd','inpatient','surgery','lab','radiology','other'])->nullable()->index();
            $table->unsignedBigInteger('case_id')->nullable()->index();

            $table->timestamps();
            $table->index(['entry_id','line_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gl_journal_lines');
        Schema::dropIfExists('gl_journal_entries');
        Schema::dropIfExists('gl_journal_batches');
    }
};
