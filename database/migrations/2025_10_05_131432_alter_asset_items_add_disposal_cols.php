<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('asset_items', function (Blueprint $table) {
      $table->enum('disposal_method', ['sale','donation','scrap'])->nullable()->after('status');
      $table->date('disposal_date')->nullable()->after('disposal_method');
      $table->unsignedBigInteger('disposal_request_id')->nullable()->after('disposal_date');

      $table->foreign('disposal_request_id')->references('id')->on('disposal_requests')->nullOnDelete();
      $table->index(['disposal_method','disposal_date']);
    });
  }

  public function down(): void {
    Schema::table('asset_items', function (Blueprint $table) {
      $table->dropForeign(['disposal_request_id']);
      $table->dropColumn(['disposal_method','disposal_date','disposal_request_id']);
    });
  }
};
