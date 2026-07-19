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
  public function up()
{
    Schema::create('clinic_service_links', function (Blueprint $table) {
        $table->id();
        $table->foreignId('clinic_id')->constrained('adm_s_clinics')->onDelete('cascade');
        $table->foreignId('service_id')->constrained('fin_services')->onDelete('cascade');
        $table->boolean('is_consultant')->default(false); // ✅ استشاري
        $table->boolean('is_doctor')->default(false);     // ✅ حالة طبيب
        $table->enum('service_type', ['examination', 'follow_up', 'operation_follow_up', 'other', 'Urgent_Consultation', 'External_Radiology_Report', 'Medical_Report'])->default('examination');
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->unsignedBigInteger('user_id')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_service_links');
    }
};
