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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('id_front')->nullable();
            $table->string('id_back')->nullable();
            $table->string('criminality')->nullable();
            $table->string('insurance_print')->nullable();
            $table->string('kaab')->nullable();
            $table->string('work_permit')->nullable();
            $table->string('union_card')->nullable();
            $table->string('union_desc')->nullable();
            $table->string('111_form')->nullable();
            $table->string('full_document')->nullable();
            $table->string('contract_document')->nullable();
            $table->string('declarations')->nullable();
            $table->string('gen_spec_prep')->nullable();
            $table->string('privileges')->nullable();
            $table->string('vaccinations')->nullable();
            $table->string('certificates')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
