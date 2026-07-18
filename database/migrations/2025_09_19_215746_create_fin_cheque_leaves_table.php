<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_cheque_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cheque_book_id')->constrained('fin_cheque_books')->cascadeOnDelete();
            $table->bigInteger('cheque_number');
            $table->enum('status', ['available','issued','approved','cleared','cancelled','bounced','reconciled'])
                  ->default('available');
            $table->foreignId('linked_cheque_id')->nullable()->constrained('fin_bank_cheques')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_cheque_leaves');
    }
};