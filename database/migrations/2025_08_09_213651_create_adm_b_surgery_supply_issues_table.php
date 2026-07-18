<?php
// database/migrations/2025_08_09_000001_create_adm_b_surgery_supply_issues_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adm_b_surgery_supply_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_booking_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->string('movement_number')->unique();
            $table->enum('status', ['draft','processed'])->default('processed');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['surgery_booking_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adm_b_surgery_supply_issues');
    }
};