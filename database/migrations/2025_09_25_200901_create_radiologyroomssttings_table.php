<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {Schema::create('rad_s_rooms', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->json('name'); // AR - EN
    $table->unsignedBigInteger('building_id')->nullable();
    $table->unsignedBigInteger('floor_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->integer('capacity_per_hour')->default(0);
    $table->softDeletes();
    $table->timestamps();

    // ✅ العلاقات
    $table->foreign('building_id')
        ->references('id')->on('adm_s_buildings')
        ->onDelete('set null');

    $table->foreign('floor_id')
        ->references('id')->on('adm_s_floors')
        ->onDelete('set null');

    $table->foreign('branch_id')
        ->references('id')->on('branches')   // ← الاسم الصح
        ->onDelete('set null');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_rooms');
    }
};
