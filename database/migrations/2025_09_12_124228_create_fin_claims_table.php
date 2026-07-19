<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_claims', function (Blueprint $table) {
            $table->bigIncrements('id'); // خلي المفتاح الأساسي BigInt
            $table->unsignedInteger('invoice_id')->nullable(); // مطابق لـ fin_invoices.id (INT UNSIGNED)
            $table->unsignedInteger('patient_id')->nullable(); // ✅ مطابق لـ patient.id (INT UNSIGNED)
            $table->unsignedBigInteger('contract_id')->nullable(); // لأن fin_contract_last_level عندك bigIncrements
            $table->enum('status', ['draft','submitted','approved','rejected','paid'])->default('draft');
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('total_claim', 12, 2)->default(0);
            $table->decimal('total_patient_share', 12, 2)->default(0);
            $table->decimal('total_contract_share', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // العلاقات
            $table->foreign('invoice_id')->references('id')->on('fin_invoices')->onDelete('set null');
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('set null');
            $table->foreign('contract_id')->references('id')->on('fin_contract_last_level')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_claims');
    }
};
