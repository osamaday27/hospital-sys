<?php

// database/migrations/2025_05_11_000000_create_post_anesthesia_recoveries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostAnesthesiaRecoveriesTable extends Migration
{
    public function up()
    {
        Schema::create('post_anesthesia_recoveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surgery_booking_id');
            $table->string('procedure_done')->nullable();
            $table->time('recovery_time_start')->nullable();
            $table->time('recovery_time_end')->nullable();
            $table->string('surgeon_name')->nullable();
            $table->string('anesthesiologist_name')->nullable();
            $table->json('anesthesia_type')->nullable();
            $table->string('sedation_type')->nullable();
            $table->json('status_vitals')->nullable();
            $table->string('level_of_consciousness')->nullable();
            $table->boolean('lines_connected')->nullable();
            $table->string('lines_description')->nullable();
            $table->json('nurse_followup')->nullable();
            $table->json('medications')->nullable();
            $table->json('fluids')->nullable();
            $table->text('unusual_events')->nullable();
            $table->json('transfer_status')->nullable();
            $table->boolean('discharge_criteria_met')->nullable();
            $table->string('discharge_location')->nullable();
            $table->time('discharge_time')->nullable();
            $table->json('aldrete_score')->nullable();
            $table->json('followup_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_anesthesia_recoveries');
    }
}
