<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('extra_oral_intra_oral_exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('id_reservation')->nullable();
            $table->unsignedBigInteger('id_visit')->nullable();
            $table->string('type_visit')->nullable();
            $table->string('general_appearance')->nullable();
            $table->string('mobility')->nullable();
            $table->string('lesions_or_scars')->nullable();
            $table->text('other_notes')->nullable();
            $table->string('lymph_node')->nullable();
            $table->text('head_neck_notes')->nullable();
            $table->string('tmj')->nullable();
            $table->text('tmj_notes')->nullable();
            $table->string('asymmetries')->nullable();
            $table->text('lips_labial_mucosa')->nullable();
            $table->text('buccal_vestibular_mucosa')->nullable();
            $table->text('hard_soft_palate')->nullable();
            $table->text('tongue')->nullable();
            $table->text('floor_of_mouth')->nullable();
            $table->text('gingiva_alveolar_mucosa')->nullable();
            $table->text('further_examinations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_oral_intra_oral_exams');
    }
};