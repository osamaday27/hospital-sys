<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            $table->unsignedBigInteger('jop_id')->nullable()->after('id'); 
            $table->unsignedBigInteger('how_to_identify_place_id')->nullable()->after('jop_id'); 

            $table->foreign('jop_id')->references('id')->on('adm_s_jops')->onDelete('set null');
            $table->foreign('how_to_identify_place_id')->references('id')->on('adm_s_how_to_identify_place')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            $table->dropForeign(['jop_id']);
            $table->dropForeign(['how_to_identify_place_id']);
            $table->dropColumn(['jop_id', 'how_to_identify_place_id']);
        });
    }
};