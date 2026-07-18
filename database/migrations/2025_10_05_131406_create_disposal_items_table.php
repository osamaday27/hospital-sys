<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('disposal_items', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('disposal_id');
      $table->unsignedBigInteger('request_item_id');
      $table->unsignedBigInteger('asset_id');

      $table->integer('qty_executed')->default(0);

      $table->decimal('exec_snapshot_cost', 14, 2)->nullable();
      $table->decimal('exec_snapshot_accum_dep', 14, 2)->nullable();
      $table->decimal('exec_snapshot_nbv', 14, 2)->nullable();
      $table->decimal('exec_snapshot_residual', 14, 2)->nullable();

      $table->string('notes', 500)->nullable();

      $table->softDeletes();
      $table->timestamps();

      $table->foreign('disposal_id')->references('id')->on('disposals')->cascadeOnDelete();
      $table->foreign('request_item_id')->references('id')->on('disposal_request_items')->cascadeOnDelete();
      $table->foreign('asset_id')->references('id')->on('asset_items')->restrictOnDelete();

      $table->index(['disposal_id','asset_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('disposal_items');
  }
};
