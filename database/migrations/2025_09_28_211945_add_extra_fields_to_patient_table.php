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
    Schema::table('patient', function (Blueprint $table) {
        $table->string('email')->nullable()->after('phone_number2');
        $table->string('blood_type', 3)->nullable()->after('gender');
        $table->text('allergies')->nullable()->after('blood_type');
        $table->text('chronic_diseases')->nullable()->after('allergies');
        $table->string('emergency_contact_name')->nullable()->after('full_address');
        $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
    });
}

public function down()
{
    Schema::table('patient', function (Blueprint $table) {
        $table->dropColumn([
            'email',
            'blood_type',
            'allergies',
            'chronic_diseases',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relation',
        ]);
    });
}

};
