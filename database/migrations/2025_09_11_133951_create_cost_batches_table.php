<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cost_batches', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();              // مثل COST-2025-09-001
            $table->date('period_start')->index();
            $table->date('period_end')->index();

            $table->enum('scope_type', ['hospital','centers','services'])->default('hospital')->index();
            $table->json('scope_ids')->nullable();             // IDs لمراكز/خدمات حسب النوع

            $table->enum('status', ['draft','running','completed','posted'])->default('draft')->index();
            $table->unsignedInteger('direct_count')->default(0);
            $table->unsignedInteger('overhead_count')->default(0);
            $table->decimal('direct_total', 18, 4)->default(0);
            $table->decimal('overhead_total', 18, 4)->default(0);
            $table->decimal('grand_total', 18, 4)->default(0);

            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cost_batches');
    }
};