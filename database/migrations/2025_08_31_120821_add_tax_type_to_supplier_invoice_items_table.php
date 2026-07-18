<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            // إضافة عمود نوع الضريبة (قيمة أو نسبة مئوية)
            $table->enum('tax_type', ['percent', 'value'])
                  ->default('percent')
                  ->after('discount'); // ضع العمود بعد عمود الخصم إذا موجود
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            $table->dropColumn('tax_type');
        });
    }
};