<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('locker_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('EGP');
            $table->enum('payment_method', ['cash', 'visa', 'bank_transfer']);
            $table->string('receipt_number')->nullable();
            $table->date('payment_date')->default(now());
            $table->string('notes')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_bank')->nullable();
            $table->string('transfer_number')->nullable();
            $table->string('transfer_bank')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['confirmed', 'pending', 'cancelled'])->default('confirmed');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};