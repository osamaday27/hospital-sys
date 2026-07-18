<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('medical_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('sheet_type'); // نوع الشيت مثل ICL أو Glaucoma أو Keratoplasty
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->json('data')->nullable(); // كل بيانات الشيت تحفظ هنا بصيغة JSON
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('medical_sheets');
    }
};