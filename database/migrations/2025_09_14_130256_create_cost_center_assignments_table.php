<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_center_assignments', function (Blueprint $table) {
            $table->id();

            // FK to cost centers
            $table->unsignedBigInteger('cost_center_id');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('cascade');

            // Polymorphic target (department/section/clinic/room/...)
            $table->string('assignable_type', 160);
            $table->unsignedBigInteger('assignable_id');

            $table->enum('status', ['active','inactive'])->default('active');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->text('notes')->nullable();

            // من قام بعملية الربط
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            // فهارس وأقيود
            $table->index(['assignable_type', 'assignable_id'], 'cca_assignable_idx');
            $table->index(['cost_center_id', 'status'], 'cca_cc_status_idx');

            // منع التكرار لنفس الربط (طالما مش محذوف)
            $table->unique(['cost_center_id', 'assignable_type', 'assignable_id', 'deleted_at'], 'cca_unique_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_center_assignments');
    }
};