<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_d_general_examinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id')->nullable(); // معرف المريض
            $table->foreign('patient_id')->references('id')->on('patient')->onDelete('set null');
            $table->unsignedInteger('visit_id')->nullable(); // معرف المريض
            $table->foreign('visit_id')->references('id')->on('patient_visits')->onDelete('set null');
            $table->string('general_appearance')->nullable();
            $table->boolean('head_neck')->default(false);
            $table->text('comment_head_neck')->nullable();
            $table->boolean('chest')->default(false);
            $table->text('comment_chest')->nullable();
            $table->boolean('abdomen')->default(false);
            $table->text('comment_abdomen')->nullable();
            $table->boolean('cns')->default(false);
            $table->text('comment_cns')->nullable();
            $table->boolean('heart')->default(false);
            $table->text('comment_heart')->nullable();
            $table->boolean('git')->default(false);
            $table->text('comment_git')->nullable();
            $table->boolean('edema')->default(false);
            $table->text('comment_edema')->nullable();
            $table->boolean('other')->default(false);
            $table->text('comment_other')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // الطبيب المسجل
            $table->softDeletes(); // للحذف الناعم
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_d_general_examinations');
    }
};