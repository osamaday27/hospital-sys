<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            // الرصيد الدفتري للصنف لحظة تسجيل الحركة
            // نفس دقة quantity لضمان الاتساق
            $table->decimal('product_store_balance', 12, 2)
                  ->nullable()
                  ->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropColumn('product_store_balance');
        });
    }
};
