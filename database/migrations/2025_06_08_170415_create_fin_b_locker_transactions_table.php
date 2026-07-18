<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fin_b_locker_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locker_id')->constrained('fin_s_locker'); // الخزنة المنفذة للحركة
            $table->enum('transaction_type', ['collection', 'payment', 'transfer_in', 'transfer_out','invoice_payment']); // نوع الحركة
            $table->decimal('amount', 15, 2); // قيمة الحركة
            $table->enum('direction', ['in', 'out']); // نوع الحركة: داخل أو خارج
            $table->string('reference_type')->nullable(); // نوع المصدر المرتبط (مثل invoice)
            $table->unsignedBigInteger('reference_id')->nullable(); // رقم المرجع
            $table->foreignId('gl_account_id')->nullable()->constrained('fin_s_gl_accounts'); // حساب دفتر الأستاذ المرتبط
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users'); // من قام بالتسجيل
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fin_b_locker_transactions');
    }
};