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
        Schema::create('hr_department', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_department')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->string('name_ne');
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
        //
    }
};
