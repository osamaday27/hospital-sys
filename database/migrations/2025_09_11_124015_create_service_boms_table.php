<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_boms', function (Blueprint $table) {
            $table->id();

            // مرتبط بخدمة من جدول fin_services
            $table->unsignedBigInteger('service_id')->index();

            $table->string('code', 50)->unique();
            $table->json('name'); // Spatie Translatable
            $table->unsignedInteger('version')->default(1)->index();

            $table->enum('status', ['draft','approved','archived'])->default('draft')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            // FK إلى fin_services
            $table->foreign('service_id')->references('id')->on('fin_services')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_boms');
    }
};