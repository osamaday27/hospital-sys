<?php

/**
 * HR Migrations List
 * 
 * This file contains all HR-related migration paths organized in proper dependency order.
 * Use this list to migrate all HR-related tables in the correct sequence.
 */

$hr_migrations = [
    // Core HR Structure Tables (no dependencies)
    'database/migrations/2024_12_21_115426_create_employee_2.php',
    'database/migrations/2024_12_28_122807_hr_create_department.php',
    'database/migrations/2024_12_28_150024_create_hr_sections.php',
    'database/migrations/2025_01_02_125059_create_hr_jobs.php',
    'database/migrations/2025_02_14_163158_create_vacation_types_table.php',
    'database/migrations/2025_02_15_193515_create_hr_public_holidays_table.php',
    'database/migrations/2025_04_23_132511_create_violations_table.php',
    'database/migrations/2025_04_24_143431_create_hr_penalties_table.php',
    'database/migrations/2025_04_26_100935_create_hr_loans_table.php',
    'database/migrations/2025_04_26_131242_create_hr_tax_conf_table.php',
    'database/migrations/2025_04_26_142911_create_hr_ins_conf_table.php',
    'database/migrations/2025_05_09_212134_create_hr_salary_rules_table.php',
    'database/migrations/2025_06_10_000001_create_hr_allowances_types_table.php',
    'database/migrations/2025_06_28_000000_create_hr_certificates_types_table.php',
    'database/migrations/2025_06_28_000001_create_hr_universities_table.php',
    'database/migrations/2025_07_12_164702_create_hr_appraisal_terms_conf_table.php',

    // Employee-related Tables (depend on employees table)
    'database/migrations/2025_01_07_150541_create_hr_contracts_table.php',
    'database/migrations/2025_01_27_200837_create_attendance_table.php',
    'database/migrations/2025_02_22_004020_create_hr_vacations_model.php',
    'database/migrations/2025_02_15_000419_create_hr_allocations_table.php',
    'database/migrations/2025_05_11_create_hr_investigations_table.php',
    'database/migrations/2025_05_17_000000_create_hr_social_insurance_table.php',
    'database/migrations/2025_06_28_130911_create_hr_certificates_table.php',

    // Salary and Payroll Tables
    'database/migrations/2025_06_10_000002_create_hr_allowances_table.php',
    'database/migrations/2025_05_10_create_hr_payslips_table.php',
    'database/migrations/2025_05_10_create_hr_salary_lines_table.php',

    // Appraisal System Tables
    'database/migrations/2025_07_12_165036_create_hr_appraisal_conf_table.php',
    'database/migrations/2025_07_12_230000_create_hr_appraisal_table.php',
    'database/migrations/2025_07_12_230100_create_hr_appraisal_details_table.php',

    // Recruitment Tables
    'database/migrations/2025_08_15_174808_create_hr_requisitions_table.php',
    'database/migrations/2025_08_15_175009_create_hr_candidates_table.php',
    'database/migrations/2025_08_15_233202_create_hr_offers_table.php',

    // Employee Table Modifications
    'database/migrations/2025_01_07_144413_add_manager_id_to_jobs_table.php',
    'database/migrations/2025_01_07_145324_add_manager_id_to_departments_table.php',
    'database/migrations/2025_01_17_123052_add_has_valid_contract_to_employee_table.php',
    'database/migrations/2025_01_21_040105_add_extra_fields_to_employees_model.php',
    'database/migrations/2025_01_22_210303_add_attachments_fields_to_employees.php',
    'database/migrations/2025_01_28_214628_add_job_details_to_employee_table.php',
    'database/migrations/2025_05_16_202321_add_attachments_fields_to_employees.php',
    'database/migrations/2025_05_18_000000_add_date_of_birth_to_employees_table.php',

    // Schedule and Attendance Modifications
    'database/migrations/2025_02_04_201651_create_schedules_table.php',
    'database/migrations/2025_02_06_024818_create_schedule_days_table.php',
    'database/migrations/2025_06_10_000004_add_total_hours_to_hr_schedules_table.php',
    'database/migrations/2025_06_10_000005_add_schedule_id_to_employees_table.php',
    'database/migrations/2025_06_10_000006_add_shortage_to_hr_attendance_table.php',
    'database/migrations/2025_06_10_000007_add_penalty_to_hr_attendance_table.php',

    // Additional HR Modifications
    'database/migrations/2025_06_09_000002_update_hr_contracts_table.php',
    'database/migrations/2025_06_10_000003_add_contract_id_to_hr_allowances_table.php',
    'database/migrations/2025_06_10_000008_update_rule_type_enum_in_hr_salary_rules_table.php',
    'database/migrations/2025_06_20_000001_add_no_salary_to_hr_vacations_types_table.php',
    'database/migrations/2025_06_28_140000_update_salary_structure_type_enum.php',
    'database/migrations/2025_07_12_221350_add_percentage_to_hr_appraisal_terms_conf_table.php',
    'database/migrations/2025_08_15_200021_add_status_to_hr_candidates_table.php',
    'database/migrations/2025_08_15_233127_add_status_to_hr_candidates_table.php',
    'database/migrations/2025_08_15_233141_add_salary_to_hr_requisitions_table.php',
    'database/migrations/2025_08_16_012443_add_tax_fields_to_hr_payslips_table.php',
    'database/migrations/2025_08_16_131247_add_penalty_fields_to_hr_penalties_table.php',
    'database/migrations/2025_08_16_143226_update_existing_salary_levels_in_contracts.php',

];

/**
 * Function to run all HR migrations
 * 
 * @return void
 */
function runHrMigrations()
{
    global $hr_migrations;
    
    echo "Starting HR Migrations...\n";
    echo "Total migrations to run: " . count($hr_migrations) . "\n\n";
    
    foreach ($hr_migrations as $index => $migration) {
        $migrationNumber = $index + 1;
        echo "[{$migrationNumber}/" . count($hr_migrations) . "] Running: {$migration}\n";
        
        // Run the migration using artisan command
        $command = "php artisan migrate --path={$migration}";
        $output = shell_exec($command . ' 2>&1');
        
        if (strpos($output, 'FAIL') !== false || strpos($output, 'Error') !== false) {
            echo "❌ FAILED: {$migration}\n";
            echo "Error: {$output}\n";
            break;
        } else {
            echo "✅ SUCCESS: {$migration}\n";
        }
        
        echo "\n";
    }
    
    echo "HR Migrations completed!\n";
}

/**
 * Function to get all HR migration paths
 * 
 * @return array
 */
function getHrMigrationPaths()
{
    global $hr_migrations;
    return $hr_migrations;
}

/**
 * Function to run specific HR migration by index
 * 
 * @param int $index
 * @return void
 */
function runHrMigrationByIndex($index)
{
    global $hr_migrations;
    
    if (!isset($hr_migrations[$index])) {
        echo "Invalid migration index: {$index}\n";
        return;
    }
    
    $migration = $hr_migrations[$index];
    echo "Running HR Migration: {$migration}\n";
    
    $command = "php artisan migrate --path={$migration}";
    $output = shell_exec($command . ' 2>&1');
    
    echo $output;
}

// Example usage:
// runHrMigrations(); // Run all HR migrations
// runHrMigrationByIndex(0); // Run first HR migration
// $paths = getHrMigrationPaths(); // Get all migration paths