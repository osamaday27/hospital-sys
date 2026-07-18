<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_appraisal_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appraisal_id');
            $table->unsignedBigInteger('term_id');
            $table->decimal('achieved_percentage', 5, 2)->default(0);
            $table->decimal('difference', 5, 2)->default(0);
            $table->timestamps();

            $table->foreign('appraisal_id')->references('id')->on('hr_appraisal')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('hr_appraisal_terms_conf')->onDelete('cascade');
            
            $table->unique(['appraisal_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_appraisal_details');
    }
}; 