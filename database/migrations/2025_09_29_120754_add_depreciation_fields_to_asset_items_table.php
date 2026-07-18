<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('asset_items', function (Blueprint $table) {
      // قيمه متبقية + تاريخ بدء الخدمة + مجمع الإهلاك + صافي القيمة + آخر شهر
      $table->decimal('residual_value', 18, 2)->default(0)->after('cost');
      $table->date('in_service_date')->nullable()->after('purchase_date');
      $table->decimal('accumulated_depreciation', 18, 2)->default(0)->after('dep_rate');
      $table->decimal('net_book_value', 18, 2)->default(0)->after('accumulated_depreciation');
      $table->date('last_depreciation_date')->nullable()->after('net_book_value');

      // لدعم UOP
      $table->integer('uop_total_estimated_units')->nullable()->after('dep_rate');
    });
  }

  public function down(): void {
    Schema::table('asset_items', function (Blueprint $table) {
      $table->dropColumn([
        'residual_value',
        'in_service_date',
        'accumulated_depreciation',
        'net_book_value',
        'last_depreciation_date',
        'uop_total_estimated_units',
      ]);
    });
  }
};
