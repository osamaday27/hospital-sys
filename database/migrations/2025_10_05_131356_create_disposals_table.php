<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('disposals', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('request_id')->unique();
      $table->enum('method', ['sale','donation','scrap']);

      $table->string('buyer_name', 190)->nullable();
      $table->string('buyer_id_number', 50)->nullable();
      $table->string('sale_invoice_no', 190)->nullable();
      $table->decimal('sale_amount', 14, 2)->nullable();
      $table->decimal('vat_amount', 14, 2)->nullable();
      $table->decimal('fees_amount', 14, 2)->nullable();
      $table->decimal('net_amount', 14, 2)->nullable();

      $table->string('donee_name', 190)->nullable();
      $table->string('donation_doc_no', 190)->nullable();

      $table->string('destruction_method', 190)->nullable();
      $table->string('destruction_doc_no', 190)->nullable();
      $table->string('witnessed_by', 190)->nullable();

      $table->decimal('exec_snapshot_total_cost', 14, 2)->nullable();
      $table->decimal('exec_snapshot_total_accum_dep', 14, 2)->nullable();
      $table->decimal('exec_snapshot_total_nbv', 14, 2)->nullable();
      $table->decimal('exec_snapshot_total_residual', 14, 2)->nullable();

      $table->unsignedBigInteger('executed_by')->nullable();
      $table->timestamp('executed_at')->nullable();

      $table->boolean('posted_flag')->default(false);
      $table->timestamp('posted_at')->nullable();

      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();

      $table->softDeletes();
      $table->timestamps();

      $table->foreign('request_id')->references('id')->on('disposal_requests')->cascadeOnDelete();
      $table->index(['method','executed_at']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('disposals');
  }
};
