<?php
// database/migrations/2025_09_10_000002_add_payment_meta_to_lockertransactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fin_b_locker_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->nullable()->after('direction');
            $table->unsignedBigInteger('bank_machine_id')->nullable()->after('bank_id');
            $table->string('reference_no')->nullable()->after('bank_machine_id'); // transaction_number
        });
    }

    public function down(): void
    {
        Schema::table('fin_b_locker_transactions', function (Blueprint $table) {
            $table->dropColumn(['bank_id', 'bank_machine_id', 'reference_no']);
        });
    }
};
