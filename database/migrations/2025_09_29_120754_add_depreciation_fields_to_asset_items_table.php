<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('asset_items')) {
            return;
        }

        Schema::table('asset_items', function (Blueprint $table) {

            if (!Schema::hasColumn('asset_items', 'residual_value')) {

                if (Schema::hasColumn('asset_items', 'cost')) {
                    $table->decimal('residual_value', 18, 2)
                        ->default(0)
                        ->after('cost');
                } else {
                    $table->decimal('residual_value', 18, 2)
                        ->default(0);
                }

            }

            if (!Schema::hasColumn('asset_items', 'in_service_date')) {

                if (Schema::hasColumn('asset_items', 'purchase_date')) {
                    $table->date('in_service_date')
                        ->nullable()
                        ->after('purchase_date');
                } else {
                    $table->date('in_service_date')
                        ->nullable();
                }

            }

            if (!Schema::hasColumn('asset_items', 'accumulated_depreciation')) {

                if (Schema::hasColumn('asset_items', 'dep_rate')) {
                    $table->decimal('accumulated_depreciation', 18, 2)
                        ->default(0)
                        ->after('dep_rate');
                } else {
                    $table->decimal('accumulated_depreciation', 18, 2)
                        ->default(0);
                }

            }

            if (!Schema::hasColumn('asset_items', 'uop_total_estimated_units')) {

                if (Schema::hasColumn('asset_items', 'dep_rate')) {
                    $table->integer('uop_total_estimated_units')
                        ->nullable()
                        ->after('dep_rate');
                } else {
                    $table->integer('uop_total_estimated_units')
                        ->nullable();
                }

            }

            if (!Schema::hasColumn('asset_items', 'net_book_value')) {

                if (Schema::hasColumn('asset_items', 'accumulated_depreciation')) {
                    $table->decimal('net_book_value', 18, 2)
                        ->default(0)
                        ->after('accumulated_depreciation');
                } else {
                    $table->decimal('net_book_value', 18, 2)
                        ->default(0);
                }

            }

            if (!Schema::hasColumn('asset_items', 'last_depreciation_date')) {

                if (Schema::hasColumn('asset_items', 'net_book_value')) {
                    $table->date('last_depreciation_date')
                        ->nullable()
                        ->after('net_book_value');
                } else {
                    $table->date('last_depreciation_date')
                        ->nullable();
                }

            }

        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('asset_items')) {
            return;
        }

        Schema::table('asset_items', function (Blueprint $table) {

            $columns = [];

            foreach ([
                'residual_value',
                'in_service_date',
                'accumulated_depreciation',
                'net_book_value',
                'last_depreciation_date',
                'uop_total_estimated_units'
            ] as $column) {

                if (Schema::hasColumn('asset_items', $column)) {
                    $columns[] = $column;
                }

            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }

        });
    }
};