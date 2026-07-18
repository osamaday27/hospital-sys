<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->constrained('fin_banks')->nullOnDelete()->after('locker_id');
            $table->foreignId('bank_machine_id')->nullable()->constrained('fin_bank_machines')->nullOnDelete()->after('bank_id');
        });
    }

    public function down()
    {
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
            $table->dropForeign(['bank_machine_id']);
            $table->dropColumn('bank_machine_id');
        });
    }
};