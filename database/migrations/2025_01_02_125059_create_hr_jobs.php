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
        Schema::create('hr_jobs', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('name_en');
            $table->string('name_ar');
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('hr_jobs');
    }
};
