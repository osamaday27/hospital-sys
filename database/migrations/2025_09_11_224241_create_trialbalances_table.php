<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_b_trial_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gl_account_id'); // الحساب
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->decimal('balance', 18, 2)->default(0);
            $table->enum('balance_type', ['debit','credit'])->default('debit');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('gl_account_id')->references('id')->on('fin_s_gl_accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_b_trial_balances');
    }
};
