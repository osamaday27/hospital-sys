<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nutritional_screenings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('user_add');

            $table->string('special_diet')->nullable();

            $table->boolean('sugar_high')->default(false);
            $table->boolean('sugar_low')->default(false);
            $table->boolean('sugar_normal')->default(false);

            $table->boolean('salt_high')->default(false);
            $table->boolean('salt_low')->default(false);
            $table->boolean('salt_normal')->default(false);

            $table->boolean('protein_high')->default(false);
            $table->boolean('protein_low')->default(false);
            $table->boolean('protein_normal')->default(false);

            $table->boolean('fat_high')->default(false);
            $table->boolean('fat_low')->default(false);
            $table->boolean('fat_normal')->default(false);

            $table->boolean('nutrition_normal')->default(false);
            $table->boolean('nutrition_nausea')->default(false);
            $table->boolean('nutrition_vomiting')->default(false);
            $table->boolean('nutrition_weight_loss')->default(false);
            $table->boolean('nutrition_dysphagia')->default(false);

            $table->boolean('mouth_normal')->default(false);
            $table->boolean('mouth_dry')->default(false);
            $table->boolean('mouth_inflammation')->default(false);

            $table->boolean('teeth_normal')->default(false);
            $table->boolean('teeth_loss')->default(false);
            $table->boolean('teeth_denture')->default(false);

            $table->boolean('women_na')->default(false);
            $table->boolean('pregnant')->default(false);
            $table->boolean('breastfeeding')->default(false);

            $table->boolean('elderly_na')->default(false);
            $table->boolean('elderly_over70')->default(false);

            $table->boolean('risk_low')->default(false);
            $table->boolean('risk_high')->default(false);

            $table->boolean('notify_doctor_yes')->default(false);
            $table->boolean('notify_doctor_no')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nutritional_screenings');
    }
};
