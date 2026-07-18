<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rad_s_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question'); // { "ar":"...", "en":"..." }
            $table->enum('answer_type', ['text', 'options'])->default('text');
            $table->json('options')->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes(); // ✅ الحذف الناعم
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rad_s_questions');
    }
};
