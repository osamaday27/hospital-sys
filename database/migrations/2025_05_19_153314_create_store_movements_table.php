<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products_data')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('store_main_date')->onDelete('cascade');
            $table->foreignId('units_id')->constrained('units')->onDelete('cascade');

            $table->decimal('quantity', 12, 2); // الكمية المحركة
            $table->string('batch_no')->nullable(); // رقم التشغيل
            $table->date('expiry_date')->nullable(); // تاريخ الصلاحية

            $table->enum('movement_type', [
                'direct_addition',      // إضافة مباشرة
                'normal_addition',      // إضافة بعد محضر فحص
                'withdrawal',           // سحب/صرف
                'transfer',             // تحويل
                'return',               // ارتجاع
                'adjustment_plus',      // تسوية بالزيادة
                'adjustment_minus',     // تسوية بالنقص
                'writeoff',             // تكهين
                'inventory'             // حركة ناتجة عن جرد
            ]);

            $table->string('reference_no')->nullable(); // رقم الفاتورة / المحضر / السند
            $table->date('movement_date')->default(now());
            $table->text('notes')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_movements');
    }
};