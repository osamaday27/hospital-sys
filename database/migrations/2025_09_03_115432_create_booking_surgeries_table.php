<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {Schema::create('booking_surgeries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('adm_b_surgery_bookings')->onDelete('cascade');
    $table->foreignId('surgery_id')->constrained('fin_services')->onDelete('cascade');
    $table->enum('side', ['right', 'left', 'both'])->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('booking_surgeries');
    }
};
