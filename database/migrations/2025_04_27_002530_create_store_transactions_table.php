<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('store_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('store_main_date')->onDelete('cascade')->unique();

            $table->boolean('opening_balances')->nullable();
            $table->boolean('add_items')->nullable();
            $table->boolean('return_to_supplier')->nullable();
            $table->boolean('return_from_department')->nullable();
            $table->boolean('return_from_customer')->nullable();
            $table->boolean('return_balance_from_store')->nullable();
            $table->boolean('issue_to_customer')->nullable();
            $table->boolean('issue_to_department')->nullable();
            $table->boolean('transfer_balance_to_store')->nullable();
            $table->boolean('inventory')->nullable();
            $table->boolean('items_destruction')->nullable();
            $table->boolean('issue_to_employee')->nullable();
            $table->boolean('return_from_employee')->nullable();
            $table->boolean('balance_adjustment_increase')->nullable();
            $table->boolean('balance_adjustment_decrease')->nullable();
            $table->boolean('issue_to_supplier')->nullable();
            $table->boolean('return_balance_to_store')->nullable();
            $table->boolean('additional_opening_balance')->nullable();
            $table->boolean('issue_to_another_branch')->nullable();
            $table->boolean('add_from_another_branch')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_transactions');
    }
}
