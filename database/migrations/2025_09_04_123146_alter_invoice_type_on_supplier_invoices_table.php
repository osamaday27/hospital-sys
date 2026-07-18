<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // توسيع أنواع الفاتورة لتشمل حالات الارتجاع
        DB::statement("
            ALTER TABLE supplier_invoices
            MODIFY COLUMN invoice_type ENUM(
                'products','services','mixed',
                'returned_products','returned_services','returned_mixed'
            ) NOT NULL DEFAULT 'products'
        ");
    }

    public function down(): void
    {
        // تحويل أي قيم ارتجاع إلى النظائر الأساسية قبل تصغير الـENUM
        DB::table('supplier_invoices')
            ->where('invoice_type', 'returned_products')
            ->update(['invoice_type' => 'products']);

        DB::table('supplier_invoices')
            ->where('invoice_type', 'returned_services')
            ->update(['invoice_type' => 'services']);

        DB::table('supplier_invoices')
            ->where('invoice_type', 'returned_mixed')
            ->update(['invoice_type' => 'mixed']);

        // إعادة العمود للقيم الأصلية فقط
        DB::statement("
            ALTER TABLE supplier_invoices
            MODIFY COLUMN invoice_type ENUM('products','services','mixed')
            NOT NULL DEFAULT 'products'
        ");
    }
};
