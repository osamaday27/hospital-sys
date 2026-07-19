<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                  ->constrained('adm_b_surgery_bookings')
                  ->onDelete('cascade');
            $table->foreignId('diagnosis_id')
                  ->constrained('m_s_diseases_list')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_diagnosis');
    }
};
