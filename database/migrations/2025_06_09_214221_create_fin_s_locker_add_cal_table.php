<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('fin_s_locker', function (Blueprint $table) {
            if (!Schema::hasColumn('fin_s_locker', 'gl_account_id')) {
                $table->unsignedBigInteger('gl_account_id')->nullable()->after('currency');
                $table->foreign('gl_account_id')->references('id')->on('fin_s_gl_accounts')->nullOnDelete();
            }

            if (!Schema::hasColumn('fin_s_locker', 'daily_limit')) {
                $table->decimal('daily_limit', 15, 2)->nullable()->after('gl_account_id');
            }

            if (!Schema::hasColumn('fin_s_locker', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('daily_limit');
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('fin_s_locker', function (Blueprint $table) {
            $table->dropForeign(['gl_account_id']);
            $table->dropColumn('gl_account_id');

            $table->dropColumn('daily_limit');

            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
