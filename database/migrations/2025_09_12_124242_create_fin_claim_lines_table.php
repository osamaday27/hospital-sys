<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_claim_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('gross', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('patient_share', 12, 2)->default(0);
            $table->decimal('contract_share', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('claim_id')->references('id')->on('fin_claims')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('fin_services')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_claim_lines');
    }
};