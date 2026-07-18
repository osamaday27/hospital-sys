<?php
// database/migrations/xxxx_xx_xx_create_adm_b_patient_anaesthesia_consents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmBPatientAnaesthesiaConsentsTable extends Migration
{
    public function up()
    {
        Schema::create('adm_b_patient_anaesthesia_consents', function (Blueprint $table) {
            $table->id();
            $table->date('consent_date')->nullable();
            $table->string('patient_name')->nullable();
            $table->string('patient_dob_age')->nullable();
            $table->enum('patient_gender', ['male', 'female'])->nullable();
            $table->string('patient_department')->nullable();
            $table->string('patient_admission_no')->nullable();
            $table->string('consenter_name')->nullable();
            $table->string('procedure_name')->nullable();
            $table->string('diagnosis_name')->nullable();
            $table->boolean('need_anaesthesia')->default(false);
            $table->boolean('need_analgesic')->default(false);
            $table->enum('selected_mode', ['anaesthesia', 'sedative', 'analgesic'])->nullable();
            $table->string('selected_mode_detail')->nullable();
            $table->boolean('type_general')->default(false);
            $table->boolean('type_nerve_primary')->default(false);
            $table->boolean('type_nerve_secondary')->default(false);
            $table->boolean('with_analgesic')->default(false);
            $table->boolean('without_analgesic')->default(false);
            $table->boolean('type_local_sedation')->default(false);
            $table->enum('final_consent_type', ['anaesthesia', 'sedative', 'analgesic'])->nullable();
            $table->string('final_patient_signature')->nullable();
            $table->date('final_consent_date')->nullable();
            $table->text('patient_unable_reasons')->nullable();
            $table->string('representative_name')->nullable();
            $table->string('representative_signature')->nullable();
            $table->date('representative_date')->nullable();
            $table->string('representative_relation')->nullable();
            $table->boolean('high_risk_patient')->nullable();
            $table->string('high_risk_reason')->nullable();
            $table->boolean('translator_help')->nullable();
            $table->string('translator_language')->nullable();
            $table->boolean('implied_consent')->nullable();
            $table->string('implied_consent_how')->nullable();
            $table->string('anesth_physician_name')->nullable();
            $table->string('anesth_physician_signature')->nullable();
            $table->date('anesth_physician_date')->nullable();
            $table->string('treating_physician_name')->nullable();
            $table->string('treating_physician_signature')->nullable();
            $table->date('treating_physician_date')->nullable();
            $table->string('witness_name')->nullable();
            $table->string('witness_signature')->nullable();
            $table->date('witness_date')->nullable();
            $table->text('emergency_procedure')->nullable();
            $table->string('emergency_physician_name')->nullable();
            $table->string('emergency_physician_signature')->nullable();
            $table->date('emergency_physician_date')->nullable();
            $table->string('consultant_witness_name')->nullable();
            $table->string('consultant_witness_signature')->nullable();
            $table->date('consultant_witness_date')->nullable();
            $table->string('additional_name')->nullable();
            $table->date('additional_date')->nullable();
            $table->string('decline_name')->nullable();
            $table->string('decline_patient_name')->nullable();
            $table->string('decline_signature')->nullable();
            $table->date('decline_date')->nullable();
            $table->time('decline_time')->nullable();
            $table->string('decline_anesth_physician_name')->nullable();
            $table->string('decline_anesth_physician_signature')->nullable();
            $table->date('decline_anesth_physician_date')->nullable();
            $table->time('decline_anesth_physician_time')->nullable();
            $table->string('decline_treating_physician_name')->nullable();
            $table->string('decline_treating_physician_signature')->nullable();
            $table->date('decline_treating_physician_date')->nullable();
            $table->time('decline_treating_physician_time')->nullable();
            $table->string('decline_witness_name')->nullable();
            $table->string('decline_witness_signature')->nullable();
            $table->date('decline_witness_date')->nullable();
            $table->time('decline_witness_time')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adm_b_patient_anaesthesia_consents');
    }
}