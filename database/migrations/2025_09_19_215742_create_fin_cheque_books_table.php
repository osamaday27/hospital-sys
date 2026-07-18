<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_cheque_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('fin_bank_accounts')->cascadeOnDelete();
            $table->string('book_number')->unique();
            $table->bigInteger('start_number');
            $table->bigInteger('end_number');
            $table->bigInteger('current_number')->nullable(); // آخر رقم مستخدم
            $table->enum('status', ['active','inactive','closed'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cheque_books');
    }
};