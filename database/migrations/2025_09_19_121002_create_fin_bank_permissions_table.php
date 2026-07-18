<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fin_bank_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('can_issue_cheque')->default(false);
            $table->boolean('can_approve_cheque')->default(false);
            $table->boolean('can_cancel_cheque')->default(false);
            $table->boolean('can_view_reports')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_bank_permissions');
    }
};