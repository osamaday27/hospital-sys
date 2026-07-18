<?php
// database/migrations/2025_05_11_000001_create_adm_b_preoperative_checklists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adm_b_preoperative_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('operation_name')->nullable();
            $table->json('equipment_status')->nullable();
            $table->string('consent_signed')->nullable();
            $table->string('site_marked')->nullable();
            $table->string('physical_exam_done')->nullable();
            $table->string('images_available')->nullable();
            $table->string('labs_available')->nullable();
            $table->string('allergy_recorded')->nullable();            
            $table->string('surgeon_signature')->nullable();
            $table->string('nurse_signature')->nullable();
            $table->date('checklist_date')->nullable();
            $table->time('checklist_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adm_b_preoperative_checklists');
    }
};