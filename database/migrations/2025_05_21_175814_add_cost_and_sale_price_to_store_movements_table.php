<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostAndSalePriceToStoreMovementsTable extends Migration
{
    public function up()
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->nullable()->after('quantity');
            $table->decimal('sale_price', 12, 2)->nullable()->after('cost_price');
        });
    }

    public function down()
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'sale_price']);
        });
    }
}