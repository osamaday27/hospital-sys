<?php
// database/migrations/2025_09_10_000001_add_payment_meta_to_receipts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_receipts', function (Blueprint $table) {
            // طريقة الدفع موجودة عندك (pay_type)؛ هنضيف أعمدة تفصيلية اختيارية
            $table->unsignedBigInteger('bank_id')->nullable()->after('pay_type');            // بنك التحويل أو بنك ماكينة الفيزا
            $table->unsignedBigInteger('bank_machine_id')->nullable()->after('bank_id');     // ماكينة الفيزا
            $table->string('transaction_number')->nullable()->after('bank_machine_id');      // رقم مرجع التحويل البنكي
            $table->string('card_holder_name')->nullable()->after('transaction_number');     // اسم حامل البطاقة
            $table->string('card_number')->nullable()->after('card_holder_name');            // 4 أو 6 أرقام أخيرة مثلاً
            $table->json('pay_meta')->nullable()->after('notes');                            // أي بيانات إضافية
        });
    }

    public function down(): void
    {
        Schema::table('fin_receipts', function (Blueprint $table) {
            $table->dropColumn([
                'bank_id',
                'bank_machine_id',
                'transaction_number',
                'card_holder_name',
                'card_number',
                'pay_meta',
            ]);
        });
    }
};