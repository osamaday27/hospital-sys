<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('m_s_medical_report_templates')) {
            return;
        }

        // إضافة الأعمدة
        Schema::table('m_s_medical_report_templates', function (Blueprint $table) {

            if (!Schema::hasColumn('m_s_medical_report_templates', 'description')) {
                $table->text('description')->nullable()->after('content');
            }

            if (!Schema::hasColumn('m_s_medical_report_templates', 'category')) {
                $table->string('category', 100)->nullable()->after('description');
            }

            if (!Schema::hasColumn('m_s_medical_report_templates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_global');
            }

            if (!Schema::hasColumn('m_s_medical_report_templates', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('m_s_medical_report_templates', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });

        // إضافة العلاقات
        Schema::table('m_s_medical_report_templates', function (Blueprint $table) {

            try {
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            } catch (\Throwable $e) {}

            try {
                $table->foreign('updated_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            } catch (\Throwable $e) {}
        });

        // إضافة الفهارس
        Schema::table('m_s_medical_report_templates', function (Blueprint $table) {

            try {
                $table->index('category');
            } catch (\Throwable $e) {}

            try {
                $table->index('is_active');
            } catch (\Throwable $e) {}

            try {
                $table->index('created_by');
            } catch (\Throwable $e) {}

            try {
                $table->index('updated_by');
            } catch (\Throwable $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('m_s_medical_report_templates')) {
            return;
        }

        Schema::table('m_s_medical_report_templates', function (Blueprint $table) {

            try {
                $table->dropForeign(['created_by']);
            } catch (\Throwable $e) {}

            try {
                $table->dropForeign(['updated_by']);
            } catch (\Throwable $e) {}

            try {
                $table->dropIndex(['category']);
            } catch (\Throwable $e) {}

            try {
                $table->dropIndex(['is_active']);
            } catch (\Throwable $e) {}

            try {
                $table->dropIndex(['created_by']);
            } catch (\Throwable $e) {}

            try {
                $table->dropIndex(['updated_by']);
            } catch (\Throwable $e) {}

            $columns = [];

            foreach ([
                'description',
                'category',
                'is_active',
                'created_by',
                'updated_by'
            ] as $column) {
                if (Schema::hasColumn('m_s_medical_report_templates', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};