<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fin_b_gl_closure_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('closure_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('closure_id')->references('id')->on('fin_b_gl_closures')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('fin_s_gl_accounts');
        });
    }

    public function down(): void {
        Schema::dropIfExists('fin_b_gl_closure_lines');
    }
};