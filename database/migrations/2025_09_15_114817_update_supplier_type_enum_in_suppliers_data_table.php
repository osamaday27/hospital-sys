<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة الخيار الثالث 'both' (كلاهما)
        DB::statement("
            ALTER TABLE suppliers_data
            MODIFY supplier_type ENUM('service','goods','both') NOT NULL
        ");
    }

    public function down(): void
    {
        // عند الرجوع، حوّل أي قيَم 'both' إلى 'goods' (أو 'service' حسب تفضيلك)
        DB::table('suppliers_data')
            ->where('supplier_type', 'both')
            ->update(['supplier_type' => 'goods']);

        // إعادة الحقل إلى القيمتين الأصليتين فقط
        DB::statement("
            ALTER TABLE suppliers_data
            MODIFY supplier_type ENUM('service','goods') NOT NULL
        ");
    }
};
