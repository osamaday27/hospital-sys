<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('disposal_request_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('request_id');
      $table->unsignedBigInteger('asset_id');

      $table->integer('qty_requested')->default(1);
      $table->integer('qty_approved')->nullable();
      $table->integer('qty_executed')->nullable();

      $table->decimal('snapshot_cost', 14, 2)->nullable();
      $table->decimal('snapshot_accum_dep', 14, 2)->nullable();
      $table->decimal('snapshot_nbv', 14, 2)->nullable();
      $table->decimal('snapshot_residual', 14, 2)->nullable();

      $table->string('remarks', 500)->nullable();

      $table->softDeletes();
      $table->timestamps();

      $table->foreign('request_id')->references('id')->on('disposal_requests')->cascadeOnDelete();
      $table->foreign('asset_id')->references('id')->on('asset_items')->restrictOnDelete();

      $table->index(['request_id','asset_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('disposal_request_items');
  }
};
