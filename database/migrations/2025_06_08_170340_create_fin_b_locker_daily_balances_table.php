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
        Schema::create('fin_b_locker_daily_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locker_id')->constrained('fin_s_locker'); // مرجع إلى جدول الخزائن
            $table->date('date'); // تاريخ اليوم
            $table->decimal('opening_balance', 15, 2)->default(0); // الرصيد الافتتاحي
            $table->decimal('closing_balance', 15, 2)->default(0); // الرصيد الختامي
            $table->decimal('actual_cash', 15, 2)->nullable(); // النقد الفعلي بعد الجرد
            $table->text('notes')->nullable(); // ملاحظات إغلاق اليوم
            $table->foreignId('created_by')->constrained('users'); // المستخدم الذي أغلق اليومية
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
        Schema::dropIfExists('fin_b_locker_daily_balances');
    }
};
