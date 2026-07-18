<?php
// database/migrations/xxxx_xx_xx_create_notes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->text('note');
            $table->enum('importance_level', ['low', 'medium', 'high'])->default('medium');

            $table->foreignId('created_by')->constrained('users');

            $table->morphs('target'); // target_type & target_id
            $table->morphs('context'); // context_type & context_id

            $table->boolean('action_required')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('due_date')->nullable();
            $table->dateTime('read_at')->nullable();

            $table->enum('status', ['new', 'done', 'ignored', 'rejected'])->default('new');
            $table->text('patient_id')->nullable();
            $table->text('reservation_id')->nullable();
            $table->text('visit_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};