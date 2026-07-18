<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
             Schema::create('m_b_dental_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('id_patient')->nullable();
            $table->string('id_visit')->nullable();
            $table->string('id_reservation')->nullable();
            $table->string('type_visit')->nullable();
            $table->date('complaint_date')->nullable();
            $table->text('complaint_description')->nullable();
            $table->string('affected_tooth')->nullable();
            $table->enum('complaint_type', ['pain', 'swelling', 'fracture', 'bleeding', 'sensitivity', 'other'])->nullable();
            $table->enum('complaint_status', ['acute', 'chronic', 'temporary'])->nullable();
            $table->string('physician_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_b_dental_complaints');
    }
};