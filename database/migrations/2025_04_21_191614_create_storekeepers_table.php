<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_keepers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')->constrained('store_main_date')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->boolean('can_issue')->default(false);     // صلاحية الصرف
            $table->boolean('can_add')->default(false);       // صلاحية الإضافة
            $table->boolean('can_inventory')->default(false); // صلاحية الجرد
            $table->boolean('can_report')->default(false);    // صلاحية التقارير
            $table->boolean('is_main_keeper')->default(false);

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_keepers');
    }
};
