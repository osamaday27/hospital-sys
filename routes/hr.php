<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\hr\EmployeeController;
use App\Http\Controllers\hr\DepartmentController;
use App\Http\Controllers\hr\sectionscontroller;
use App\Http\Controllers\hr\HrSettingsController;
use App\Http\Controllers\hr\payrollprogramscontroller;
use App\Http\Controllers\hr\payrollsettingscontroller;
use App\Http\Controllers\hr\employeesprogramscontroller;
use App\Http\Controllers\hr\employeessettingscontroller;
use App\Http\Controllers\hr\attendanceprograms;
use App\Http\Controllers\hr\attendancesettings;
use App\Http\Controllers\hr\vacationsprograms;
use App\Http\Controllers\hr\vacationssettings;
use App\Http\Controllers\hr\appraisalprograms;
use App\Http\Controllers\hr\appraisalsettings;
use App\Http\Controllers\hr\hruniversitiescontroller;
use App\Http\Controllers\hr\hr_appraisal_controller;
use App\Http\Controllers\hr\hrrequisitonscontroller;
use App\Http\Controllers\hr\hrcandidatescontroller;
use App\Http\Controllers\hr\hrrecruitmentprograms;
use App\Http\Controllers\hr\hrrecruitmentsettings;


Auth::routes(['verify' => true]);
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        //medical_record
       Route::group(['namespace'=>'hr'],function()
        {
            Route::resource('employees', 'EmployeeController');
            Route::get('/get-employees', 'EmployeeController@get_employees')->name('get-employees');
            Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
            Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
            Route::post('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::get('/hr-settings', [HrSettingsController::class, 'index'])->name('hr.settings');



            Route::get('/hr-payroll-programs', [payrollprogramscontroller::class, 'index'])->name('hr.payroll_programs');
            Route::get('/hr-payroll-settings', [payrollsettingscontroller::class, 'index'])->name('hr.payroll_settings');
            Route::get('/hr-employees-programs', [employeesprogramscontroller::class, 'index'])->name('hr.employees_programs');
            Route::get('/hr-employees-settings', [employeessettingscontroller::class, 'index'])->name('hr.employees_settings');

            Route::get('/hr-attendance-programs', [attendanceprograms::class, 'index'])->name('hr.attendance_programs');
            Route::get('/hr-attendance-settings', [attendancesettings::class, 'index'])->name('hr.attendance_settings');

            Route::get('/hr-vacations-programs', [vacationsprograms::class, 'index'])->name('hr.vacations_programs');
            Route::get('/hr-vacations-settings', [vacationssettings::class, 'index'])->name('hr.vacations_settings');

            Route::get('/hr-appraisal-programs', [appraisalprograms::class, 'index'])->name('hr.appraisal_programs');
            Route::get('/hr-appraisal-settings', [appraisalsettings::class, 'index'])->name('hr.appraisal_settings');

            Route::get('/hr-recruitment-programs', [hrrecruitmentprograms::class, 'index'])->name('hr.recruitment_programs');
            Route::get('/hr-recruitment-settings', [hrrecruitmentsettings::class, 'index'])->name('hr.recruitment_settings');

            Route::resource('hr_department', 'DepartmentController');
            Route::post('/hr_department/{id}', [DepartmentController::class, 'update'])->name('hr_department.update');

            Route::resource('hr_section', 'sectionscontroller');
            Route::post('/hr_section/{id}', [sectionscontroller::class, 'update'])->name('hr_section.update');

            Route::resource('hr_jobs', 'jobscontroller');
            Route::get('/get-jobs', 'jobscontroller@get_jobs')->name('get-jobs');
            Route::get('hr_jobs/{id}/edit', 'jobscontroller@edit')->name('hr_jobs.edit');
            Route::get('hr_jobs/{id}/show', 'jobscontroller@show')->name('hr_jobs.show');
            Route::delete('hr_jobs/{id}', 'jobscontroller@destroy')->name('hr_jobs.destroy');
            Route::get('hr_jobs/{id}/test', 'jobscontroller@test')->name('hr_jobs.test');

            Route::resource('hr_contracts', 'hrcontractscontroller');
            Route::get('/get-contracts', 'hrcontractscontroller@get_contracts')->name('get-contracts');
            Route::get('hr_contracts/{id}/edit', 'hrcontractscontroller@edit')->name('hr_contracts.edit');
            Route::get('hr_contracts/{id}/show', 'hrcontractscontroller@show')->name('hr_contracts.show');
            Route::delete('hr_contracts/{id}', 'hrcontractscontroller@destroy')->name('hr_contracts.destroy');
            Route::get('/hr_contracts/get-department-job/{employeeId}', 'hrcontractscontroller@getDepartmentJobByEmployee')->name('hr_contracts.getDepartmentJobByEmployee');
            Route::get('/calculate-salary-components/{basic_salary}', 'hrcontractscontroller@calculateSalaryComponents')->name('calculate.salary.components');

            Route::resource('hr_attendance', 'hrattendancecontroller');
            Route::get('/get-attendance', 'hrattendancecontroller@get_attendance')->name('get-attendance');
            Route::get('hr_attendance/{id}/edit', 'hrattendancecontroller@edit')->name('hr_attendance.edit');
            Route::get('hr_attendance/{id}/show', 'hrattendancecontroller@show')->name('hr_attendance.show');
            Route::delete('hr_attendance/{id}', 'hrattendancecontroller@destroy')->name('hr_attendance.destroy');
            Route::post('hr_attendance/{id}/update-penalty', 'hrattendancecontroller@updatePenalty')->name('hr_attendance.update_penalty');
            Route::post('hr_attendance/upload', 'hrattendancecontroller@upload')->name('hr_attendance.upload');
            Route::get('hr_attendance/test-datetime-parsing', 'hrattendancecontroller@testDateTimeParsing')->name('hr_attendance.test_datetime_parsing');
            Route::get('hr_attendance/test-create-attendance', 'hrattendancecontroller@testCreateAttendance')->name('hr_attendance.test_create_attendance');

            Route::resource('hr_schedules', 'hrschedulescontrollers');
            Route::get('/get-schedules', 'hrschedulescontrollers@get_schedules')->name('get-schedules');
            Route::get('hr_schedules/{id}/edit', 'hrschedulescontrollers@edit')->name('hr_schedules.edit');
            Route::get('hr_schedules/{id}/show', 'hrschedulescontrollers@show')->name('hr_schedules.show');
            Route::delete('hr_schedules/{id}', 'hrschedulescontrollers@destroy')->name('hr_schedules.destroy');

            Route::resource('hr_vacation_types', 'hrvacationtypescontroller');
            Route::get('/get-vacation-types', 'hrvacationtypescontroller@get_vacation_types')->name('get-vacation-types');
            Route::get('hr_vacation_types/{id}/edit', 'hrvacationtypescontroller@edit')->name('hr_vacation_types.edit');
            Route::get('hr_vacation_types/{id}/show', 'hrvacationtypescontroller@show')->name('hr_vacation_types.show');
            Route::delete('hr_vacation_types/{id}', 'hrvacationtypescontroller@destroy')->name('hr_vacation_types.destroy');


            Route::resource('hr_allocations', 'hrallocationscontroller');
            Route::get('/get-allocations', 'hrallocationscontroller@get_allocations')->name('get-allocations');
            Route::get('hr_allocations/{id}/edit', 'hrallocationscontroller@edit')->name('hr_allocations.edit');
            Route::get('hr_allocations/{id}/show', 'hrallocationscontroller@show')->name('hr_allocations.show');
            Route::delete('hr_allocations/{id}', 'hrallocationscontroller@destroy')->name('hr_allocations.destroy');

            Route::resource('hr_public_holidays', 'hrpublicholidayscontroller');
            Route::get('/get-public-holidays', 'hrpublicholidayscontroller@get_public_holidays')->name('get-public-holidays');
            Route::get('hr_public_holidays/{id}/edit', 'hrpublicholidayscontroller@edit')->name('hr_public_holidays.edit');
            Route::get('hr_public_holidays/{id}/show', 'hrpublicholidayscontroller@show')->name('hr_public_holidays.show');
            Route::delete('hr_public_holidays/{id}', 'hrpublicholidayscontroller@destroy')->name('hr_public_holidays.destroy');

            Route::resource('hr_vacations', 'hrvacationscontroller');
            Route::get('/get-vacations', 'hrvacationscontroller@get_vacations')->name('get-vacations');
            Route::get('hr_vacations/{id}/edit', 'hrvacationscontroller@edit')->name('hr_vacations.edit');
            Route::get('hr_vacations/{id}/show', 'hrvacationscontroller@show')->name('hr_vacations.show');
            Route::delete('hr_vacations/{id}', 'hrvacationscontroller@destroy')->name('hr_vacations.destroy');
            Route::get('/hr/vacations/remaining-balance', 'hrvacationscontroller@getRemainingBalance')->name('hr.vacations.remaining_balance');

            Route::resource('hr_violations', 'hrviolationscontroller');
            Route::get('/get-violations', 'hrviolationscontroller@get_violations')->name('get-violations');
            Route::get('hr_violations/{id}/edit', 'hrviolationscontroller@edit')->name('hr_violations.edit');
            Route::get('hr_violations/{id}/show', 'hrviolationscontroller@show')->name('hr_violations.show');
            Route::delete('hr_violations/{id}', 'hrviolationscontroller@destroy')->name('hr_violations.destroy');


            Route::resource('hr_penalties', 'hrpenaltiescontroller');
            Route::get('/get-penalties', 'hrpenaltiescontroller@get_penalties')->name('get-penalties');
            Route::get('hr_penalties/{id}/edit', 'hrpenaltiescontroller@edit')->name('hr_penalties.edit');
            Route::get('hr_penalties/{id}/show', 'hrpenaltiescontroller@show')->name('hr_penalties.show');
            Route::delete('hr_penalties/{id}', 'hrpenaltiescontroller@destroy')->name('hr_penalties.destroy');


            Route::resource('hr_loans', 'hrloanscontroller');
            Route::get('/get-loans', 'hrloanscontroller@get_loans')->name('get-loans');
            Route::get('hr_loans/{id}/edit', 'hrloanscontroller@edit')->name('hr_loans.edit');
            Route::get('hr_loans/{id}/show', 'hrloanscontroller@show')->name('hr_loans.show');
            Route::delete('hr_loans/{id}', 'hrloanscontroller@destroy')->name('hr_loans.destroy');
            Route::post('hr_loans/{loan}/approve', 'hrloanscontroller@approve')->name('hr.loans.approve');
            Route::post('hr_loans/{loan}/reject', 'hrloanscontroller@reject')->name('hr.loans.reject');

            Route::resource('hr_tax_conf', 'hrtaxconfcontroller');
            Route::get('hr_tax_conf/{id}/edit', 'hrtaxconfcontroller@edit')->name('hr_tax_conf.edit');
            Route::get('hr_tax_conf/{id}/show', 'hrtaxconfcontroller@show')->name('hr_tax_conf.show');
            Route::delete('hr_tax_conf/{id}', 'hrtaxconfcontroller@destroy')->name('hr_tax_conf.destroy');
            Route::post('hr_tax_conf/{hrTaxConf}/toggle-status', 'hrtaxconfcontroller@toggleStatus')->name('hr-tax-confs.toggle-status');

            Route::resource('hr_ins_conf', 'hrinsconfcontroller');
            Route::get('hr_ins_conf/{id}/edit', 'hrinsconfcontroller@edit')->name('hr_ins_conf.edit');
            Route::get('hr_ins_conf/{id}/show', 'hrinsconfcontroller@show')->name('hr_ins_conf.show');
            Route::delete('hr_ins_conf/{id}', 'hrinsconfcontroller@destroy')->name('hr_ins_conf.destroy');
            Route::post('hr_ins_conf/{hrInsConf}/toggle-status', 'hrinsconfcontroller@toggleStatus')->name('hr-ins-confs.toggle-status');

            Route::resource('hr_salary_rules', 'hrsalaryrulescontroller');
            Route::get('/get-salary-rules', 'hrsalaryrulescontroller@get_salary_rules')->name('get-salary-rules');
            Route::get('hr_salary_rules/{id}/edit', 'hrsalaryrulescontroller@edit')->name('hr_salary_rules.edit');
            Route::get('hr_salary_rules/{id}/show', 'hrsalaryrulescontroller@show')->name('hr_salary_rules.show');
            Route::delete('hr_salary_rules/{id}', 'hrsalaryrulescontroller@destroy')->name('hr_salary_rules.destroy');

            Route::resource('hr_payslips', 'hrpayslipscontroller');
            Route::get('/get-payslips', 'hrpayslipscontroller@get_payslips')->name('get-payslips');
            Route::get('hr_payslips/{id}/edit', 'hrpayslipscontroller@edit')->name('hr_payslips.edit');
            Route::get('hr_payslips/{id}/show', 'hrpayslipscontroller@show')->name('hr_payslips.show');
            Route::delete('hr_payslips/{id}', 'hrpayslipscontroller@destroy')->name('hr_payslips.destroy');
            Route::get('/hr_payslips/employee-contract/{employeeId}', 'hrpayslipscontroller@getEmployeeContractInfo')->name('hr_payslips.employee_contract');

            Route::resource('hr_investigations', 'hrinvestigationscontroller');
            Route::get('/get-investigations', 'hrinvestigationscontroller@get_investigations')->name('get-investigations');
            Route::get('hr_investigations/{id}/edit', 'hrinvestigationscontroller@edit')->name('hr_investigations.edit');
            Route::get('hr_investigations/{id}/show', 'hrinvestigationscontroller@show')->name('hr_investigations.show');
            Route::delete('hr_investigations/{id}', 'hrinvestigationscontroller@destroy')->name('hr_investigations.destroy');

            Route::resource('hr_social_insurance', 'hrsocialinsurancecontroller');
            Route::get('/get-social-insurance', 'hrsocialinsurancecontroller@get_social_insurance')->name('get-social-insurance');
            Route::get('hr_social_insurance/{id}/edit', 'hrsocialinsurancecontroller@edit')->name('hr_social_insurance.edit');
            Route::get('hr_social_insurance/{id}/show', 'hrsocialinsurancecontroller@show')->name('hr_social_insurance.show');
            Route::delete('hr_social_insurance/{id}', 'hrsocialinsurancecontroller@destroy')->name('hr_social_insurance.destroy');

            Route::resource('hr_allowances_types', 'hrallowancestypescontroller');
            Route::get('get_allowances_types', ['as' => 'hr_allowances_types.get_allowances_types', 'uses' => 'hrallowancestypescontroller@get_allowances_types']);

            Route::resource('hr_allowances', 'hrallowancescontroller');
            Route::get('get_allowances', ['as' => 'hr_allowances.get_allowances', 'uses' => 'hrallowancescontroller@get_allowances']);

            Route::resource('hr_certificates_types', 'hrcertificatestypescontroller');
            Route::get('get_certificates_types', ['as' => 'hr_certificates_types.get_certificates_types', 'uses' => 'hrcertificatestypescontroller@get_certificates_types']);

            Route::resource('hr_universities', 'hruniversitiescontroller');
            Route::get('get_universities', ['as' => 'hr_universities.get_universities', 'uses' => 'hruniversitiescontroller@get_universities']);

            Route::resource('hr_appraisal_conf', 'hrappraisalconfcontroller');
            Route::get('/get-appraisal-conf', 'hrappraisalconfcontroller@get_appraisal_conf')->name('get-appraisal-conf');
            Route::get('hr_appraisal_conf/{id}/edit', 'hrappraisalconfcontroller@edit')->name('hr_appraisal_conf.edit');
            Route::get('hr_appraisal_conf/{id}/show', 'hrappraisalconfcontroller@show')->name('hr_appraisal_conf.show');
            Route::delete('hr_appraisal_conf/{id}', 'hrappraisalconfcontroller@destroy')->name('hr_appraisal_conf.destroy');

            // Main Appraisal Routes
            Route::get('/hr_appraisal/data', [hr_appraisal_controller::class, 'getData'])->name('hr.appraisal.data');
            Route::get('/hr_appraisal/get-employee-data', [hr_appraisal_controller::class, 'getEmployeeData'])->name('hr.appraisal.get_employee_data');
            Route::resource('hr_appraisal', hr_appraisal_controller::class);
            Route::delete('/hr_appraisal/{id}', [hr_appraisal_controller::class, 'destroy'])->name('hr.appraisal.destroy');

            // Recruitment Routes
            // Job Requisitions
            Route::resource('hr_requisitions', 'hrrequisitonscontroller');
            Route::get('/get-requisitions', 'hrrequisitonscontroller@getData')->name('hr_requisitions.data');
            Route::post('/hr_requisitions/{id}/approve', 'hrrequisitonscontroller@approve')->name('hr_requisitions.approve');
            Route::post('/hr_requisitions/{id}/refuse', 'hrrequisitonscontroller@refuse')->name('hr_requisitions.refuse');

            // Candidates
            Route::resource('hr_candidates', 'hrcandidatescontroller');
            Route::get('/get-candidates', 'hrcandidatescontroller@getData')->name('hr_candidates.data');
            Route::post('hr_candidates/{id}/update-status', 'hrcandidatescontroller@updateStatus')->name('hr_candidates.update_status');

            // Offers
            Route::resource('hr_offers', 'hrofferscontroller');
            Route::get('/get-offers', 'hrofferscontroller@getData')->name('hr_offers.data');
            Route::post('hr_offers/{id}/approve', 'hrofferscontroller@approve')->name('hr_offers.approve');
            Route::post('hr_offers/{id}/reject', 'hrofferscontroller@reject')->name('hr_offers.reject');

            // Tax Calculations
            Route::get('/tax/test', 'TaxCalculationController@testTaxCalculation')->name('tax.test');
            Route::get('/tax/config', 'TaxCalculationController@getTaxConfiguration')->name('tax.config');
            Route::get('/tax/employee/{id}/preview', 'TaxCalculationController@previewEmployeeTax')->name('tax.employee.preview');
            Route::post('/tax/bulk-calculation', 'TaxCalculationController@bulkTaxCalculation')->name('tax.bulk');
            Route::post('/tax/recalculate-payslips', 'TaxCalculationController@recalculatePayslipTaxes')->name('tax.recalculate');

            // Hourly payslip debug route
            Route::get('/debug/hourly/{employeeId}/{dateFrom}/{dateTo}', 'hrpayslipscontroller@testHourlyCalculation')->name('debug.hourly');


        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);
