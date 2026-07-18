<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hr_tax_conf', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->float('exemption_margin', 12, 2)->default(20000.00);
            $table->float('segment_1', 12, 2)->default(40000.00);
            $table->float('segment_2', 12, 2)->default(55000.00);
            $table->float('segment_3', 12, 2)->default(70000.00);
            $table->float('segment_4', 12, 2)->default(200000.00);
            $table->float('segment_5', 12, 2)->default(400000.00);
            $table->float('segment_6', 12, 2)->default(600000.00);
            $table->float('segment_7', 12, 2)->default(700000.00);
            $table->float('segment_8', 12, 2)->default(800000.00);
            $table->float('segment_9', 12, 2)->default(900000.00);
            $table->float('segment_10', 12, 2)->default(1200000.00);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_tax_conf');
    }
};
