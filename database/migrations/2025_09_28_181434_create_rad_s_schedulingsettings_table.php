<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rad_s_schedulingsettings', function (Blueprint $table) {
            $table->id();

            // نخلي room_id و device_id أعمدة عادية بدون FK
            $table->unsignedBigInteger('room_id')->nullable()->index();
            $table->unsignedBigInteger('device_id')->nullable()->index();

            $table->enum('shift_type', ['morning', 'evening', 'night']);
            $table->time('start_time');
            $table->time('end_time');

            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_maintenance')->default(false);

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rad_s_schedulingsettings');
    }
};
