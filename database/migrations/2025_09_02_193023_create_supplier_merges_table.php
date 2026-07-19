<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('supplier_merges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_supplier_id');   // المورد الأساسي
            $table->unsignedBigInteger('merged_supplier_id'); // المورد المدموج
            $table->unsignedBigInteger('user_id');            // المستخدم اللي عمل الدمج
            $table->json('affected_tables')->nullable();      // اللوج بتاع السجلات المتأثرة
            $table->boolean('is_reverted')->default(false);   // هل اتعمل رجوع؟
            $table->timestamps();

            $table->foreign('main_supplier_id')->references('id')->on('suppliers_data');
            $table->foreign('merged_supplier_id')->references('id')->on('suppliers_data');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_merges');
    }
};
