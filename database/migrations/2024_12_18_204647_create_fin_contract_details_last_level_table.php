<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fin_contracts_details_last_level', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('last_level_id')->nullable(); // قائمة التعاقد
            $table->enum('status', ['active', 'inactive'])->default('active'); // الحالة
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أنشأ القائمة
            $table->decimal('discount_percentage_all_services')->nullable();
            $table->decimal('patient_contribution_all_services')->nullable();
            $table->decimal('discount_percentage_clinic_services')->nullable();
            $table->decimal('patient_contribution_clinic_services')->nullable();
            $table->decimal('discount_percentage_inpatient_services')->nullable();
            $table->decimal('patient_contribution_inpatient_services')->nullable();
            $table->decimal('discount_percentage_surgical_services')->nullable();
            $table->decimal('patient_contribution_surgical_services')->nullable();
            $table->decimal('discount_percentage_lab_services')->nullable();
            $table->decimal('patient_contribution_lab_services')->nullable();
            $table->decimal('discount_percentage_emergency_services')->nullable();
            $table->decimal('patient_contribution_emergency_services')->nullable();
            $table->decimal('discount_percentage_rad_services')->nullable();
            $table->decimal('patient_contribution_rad_services')->nullable();
            $table->decimal('discount_percentage_medications')->nullable();
            $table->decimal('patient_contribution_medications')->nullable();
            $table->decimal('discount_percentage_supplies')->nullable();
            $table->decimal('patient_contribution_supplies')->nullable();
            $table->text('services_require_approval')->nullable();
            $table->text('services_not_covered')->nullable();
            $table->foreign('last_level_id')->references('id')->on('fin_contract_last_level')->onDelete('set null');
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
        Schema::dropIfExists('fin_contracts_details_last_level');
    }
};