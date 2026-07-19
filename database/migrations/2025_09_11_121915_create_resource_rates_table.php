<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_rates', function (Blueprint $table) {
            $table->id();

            // تعريف المورد
            $table->string('code', 50)->unique();
            $table->json('name'); // translatable

            // نوع المورد
            $table->enum('type', [
                'doctor', 'nurse', 'room', 'device', 'bed', 'other'
            ])->default('other')->index();

            // المركز المرتبط (cost center)
            $table->unsignedBigInteger('cost_center_id')->nullable()->index();

            // معدل التكلفة
            $table->enum('unit', ['minute','hour','day'])->default('minute');
            $table->decimal('rate', 18, 2)->default(0);

            // حالة
            $table->enum('status', ['active','inactive'])->default('active')->index();

            // تتبع
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_rates');
    }
};
