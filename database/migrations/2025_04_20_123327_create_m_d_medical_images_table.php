<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::create('m_d_medical_images', function (Blueprint $table) {
            $table->id();
            $table->string('path')->nullable();         // مسار الصورة
            $table->string('type')->nullable();         // نوع الصورة
            $table->text('notes')->nullable();          // ملاحظات
              // معلومات أساسية
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_visit');
            $table->unsignedBigInteger('id_reservation');
            $table->string('type_visit');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_d_medical_images');
    }
};
