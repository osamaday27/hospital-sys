<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('disposal_requests', function (Blueprint $table) {
      $table->id();
      $table->string('request_no')->unique();

      $table->enum('method', ['sale','donation','scrap']);
      $table->enum('status', [
        'draft','submitted','under_review','approved','rejected','cancelled','executed','closed'
      ])->default('draft');

      $table->string('reason', 500)->nullable();
      $table->text('notes')->nullable();

      $table->timestamp('requested_at')->nullable();
      $table->timestamp('submitted_at')->nullable();
      $table->timestamp('approved_at')->nullable();
      $table->timestamp('executed_at')->nullable();
      $table->timestamp('closed_at')->nullable();

      $table->unsignedBigInteger('requested_by')->nullable(); // users.id
      $table->unsignedBigInteger('submitted_by')->nullable(); // users.id

      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();

      $table->softDeletes();
      $table->timestamps();

      $table->index(['method','status']);
      $table->index(['requested_by','submitted_by']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('disposal_requests');
  }
};
