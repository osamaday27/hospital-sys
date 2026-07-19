<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overhead_allocation_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id')->index();
            $table->unsignedBigInteger('cost_center_id')->index();
            $table->decimal('percent', 8, 4)->nullable();       // لو method = percent
            $table->decimal('driver_value', 18, 4)->nullable();  // لو method = driver
            $table->decimal('allocated_amount', 18, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rule_id')->references('id')->on('overhead_allocation_rules')->cascadeOnDelete();
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overhead_allocation_lines');
    }
};
