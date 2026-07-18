<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ تعديل جدول الأدوية (medications)
        Schema::table('medications', function (Blueprint $table) {
            if (!Schema::hasColumn('medications', 'scientific_name')) {
                $table->string('scientific_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('medications', 'category')) {
                $table->string('category')->nullable()->after('scientific_name');
            }
            if (!Schema::hasColumn('medications', 'description')) {
                // نعدّل النوع ليكون JSON بدل text لتخزين ترجمات أو بيانات متعددة
                $table->json('description')->nullable()->change();
            }
            if (!Schema::hasColumn('medications', 'default_unit_id')) {
                $table->foreignId('default_unit_id')->nullable()->constrained('medication_units')->onDelete('set null');
            }
            if (!Schema::hasColumn('medications', 'default_duration_id')) {
                $table->foreignId('default_duration_id')->nullable()->constrained('medication_durations')->onDelete('set null');
            }
            if (!Schema::hasColumn('medications', 'default_taken_id')) {
                $table->foreignId('default_taken_id')->nullable()->constrained('medication_takens')->onDelete('set null');
            }
            if (!Schema::hasColumn('medications', 'default_dose')) {
                $table->decimal('default_dose', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('medications', 'is_favorite')) {
                $table->boolean('is_favorite')->default(false);
            }
            if (!Schema::hasColumn('medications', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('medications', 'warnings')) {
                $table->json('warnings')->nullable();
            }
            if (!Schema::hasColumn('medications', 'side_effects')) {
                $table->json('side_effects')->nullable();
            }
            if (!Schema::hasColumn('medications', 'contraindications')) {
                $table->json('contraindications')->nullable();
            }
        });

        // ✅ تعديل جدول الوصفات (prescriptions)
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('prescriptions', 'diagnosis')) {
                $table->text('diagnosis')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('prescriptions', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // ✅ إنشاء الجداول المساعدة إن لم تكن موجودة
        if (!Schema::hasTable('medication_units')) {
            Schema::create('medication_units', function (Blueprint $table) {
                $table->id();
                $table->json('name');
                $table->json('abbreviation')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('medication_durations')) {
            Schema::create('medication_durations', function (Blueprint $table) {
                $table->id();
                $table->json('name');
                $table->integer('days')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('medication_takens')) {
            Schema::create('medication_takens', function (Blueprint $table) {
                $table->id();
                $table->json('name');
                $table->json('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🧹 حذف الأعمدة المضافة في medications
        Schema::table('medications', function (Blueprint $table) {
            $columns = [
                'scientific_name', 'category', 'default_unit_id', 'default_duration_id',
                'default_taken_id', 'default_dose', 'is_favorite', 'is_active',
                'warnings', 'side_effects', 'contraindications'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('medications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 🧹 حذف الأعمدة المضافة في prescriptions
        Schema::table('prescriptions', function (Blueprint $table) {
            $columns = ['notes', 'diagnosis', 'deleted_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('prescriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 🧹 حذف الجداول المضافة إذا كانت موجودة
        Schema::dropIfExists('medication_units');
        Schema::dropIfExists('medication_durations');
        Schema::dropIfExists('medication_takens');
    }
};