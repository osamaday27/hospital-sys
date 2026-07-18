<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->string('medication_text')->nullable()->after('medication_id');
            $table->string('unit_text')->nullable()->after('unit_id');
            $table->string('duration_text')->nullable()->after('duration_id');
            $table->string('taken_text')->nullable()->after('taken_id');
        });
    }

    public function down()
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn(['medication_text', 'unit_text', 'duration_text', 'taken_text']);
        });
    }
};