<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('code', 50)->unique();
            // name JSON للترجمة (Spatie Translatable)
            $table->json('name');
            $table
                ->enum('type', [
                    // وحدات طبية
                    'clinic',
                    'surgery',
                    'lab',
                    'imaging',
                    'inpatient',
                    'pharmacy',
                    'emergency',
                    'icu',
                    'nicu',
                    'dialysis',
                    'physiotherapy',
                    'endoscopy',
                    'dental',
                    'ophthalmology',
                    'maternity',
                    'oncology',
                    'cardiology',

                    // وحدات إدارية وخدمية
                    'admin',
                    'hr',
                    'finance',
                    'it',
                    'maintenance',
                    'housekeeping',
                    'security',
                    'catering',
                    'laundry',
                    'transportation',
                    'store',

                    // مراكز داعمة
                    'research',
                    'education',
                    'marketing',
                    'other',
                ])
                ->default('other')
                ->index();

            $table
                ->enum('status', ['active', 'inactive'])
                ->default('active')
                ->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // من قام بالإنشاء/التعديل
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('cost_centers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};
