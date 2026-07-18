<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('fin_s_locker', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('name_en'); // كود الخزنة
            $table->decimal('current_balance', 15, 2)->default(0)->after('has_daily_limit'); // الرصيد الحالي
            $table->foreignId('branch_id')->nullable()->constrained('branches')->after('current_balance'); // الربط بالفرع
            $table->foreignId('created_by')->nullable()->constrained('users')->after('branch_id'); // من أنشأ الخزنة
        });
    }

    public function down()
    {
        Schema::table('fin_s_locker', function (Blueprint $table) {
            $table->dropColumn(['code', 'current_balance', 'branch_id', 'created_by']);
        });
    }
};