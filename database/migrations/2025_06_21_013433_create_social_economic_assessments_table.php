<?php
// database/migrations/2025_06_21_000000_create_social_economic_assessments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialEconomicAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::create('social_economic_assessments', function (Blueprint $table) {
            $table->id();
               // Relations
               $table->unsignedBigInteger('patient_id');
               $table->unsignedBigInteger('visit_id')->nullable();
               $table->unsignedBigInteger('reservation_id')->nullable();
               $table->unsignedBigInteger('user_add')->nullable();

            // الحالة المعيشية
            $table->boolean('living_alone')->default(false);
            $table->boolean('living_with_family')->default(false);
            $table->boolean('living_with_others')->default(false);
            $table->string('living_other_details')->nullable();
            
            // احتياجات خاصة
            $table->boolean('special_needs_none')->default(false);
            $table->boolean('hearing')->default(false);
            $table->boolean('speech')->default(false);
            $table->boolean('language_barrier')->default(false);
            $table->string('other_needs_details')->nullable();
            
            // الحالة الاجتماعية
            $table->boolean('single')->default(false);
            $table->boolean('married')->default(false);
            $table->boolean('has_children')->default(false);
            $table->integer('number_of_children')->nullable();
            
            // التدخين
            $table->boolean('smoking_no')->default(false);
            $table->boolean('smoking_yes')->default(false);
            $table->boolean('smoking_previous')->default(false);
            $table->integer('cigarettes_per_day')->nullable();
            
            // الفحص النفسي
            $table->boolean('no_psychological_issues')->default(false);
            $table->boolean('anxiety')->default(false);
            $table->boolean('anger')->default(false);
            $table->boolean('depression')->default(false);
            
            // التغطية الطبية
            $table->boolean('can_afford')->default(false);
            $table->boolean('insured')->default(false);
            $table->boolean('cannot_afford')->default(false);
            
            // آثار العنف
            $table->boolean('no_violence')->default(false);
            $table->boolean('violence_present')->default(false);
            $table->string('violence_details')->nullable();
            
            // الحالة الوظيفية
            $table->boolean('not_working')->default(false);
            $table->boolean('working')->default(false);
            $table->string('occupation')->nullable();
            
            // تنبيه الطبيب
            $table->boolean('notify_doctor_yes')->default(false);
            $table->boolean('notify_doctor_no')->default(false);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_economic_assessments');
    }
}
