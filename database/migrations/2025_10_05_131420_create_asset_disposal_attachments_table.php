<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('asset_disposal_attachments', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('request_id');
      $table->enum('type', ['request','approval','execution','other'])->default('request');
      $table->string('file_path', 500);
      $table->string('original_name', 255)->nullable();
      $table->text('notes')->nullable();

      $table->unsignedBigInteger('uploaded_by')->nullable();

      $table->softDeletes();
      $table->timestamps();

      $table->foreign('request_id')->references('id')->on('disposal_requests')->cascadeOnDelete();
      $table->index(['request_id','type']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('asset_disposal_attachments');
  }
};
