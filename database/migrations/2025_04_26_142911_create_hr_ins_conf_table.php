<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hr_ins_conf', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->float('employee_share', 12, 2)->default(11.0);
            $table->float('company_share', 12, 2)->default(18.75);
            $table->float('social_insurance_margin', 12, 2)->default(12600.00);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_tax_conf');
    }
};
