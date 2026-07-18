<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Auth::routes(['verify' => true]);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        /*
        |--------------------------------------------------------------------------
        | موديول البنوك
        |--------------------------------------------------------------------------
        */
        Route::group(['namespace' => 'finance\banks'], function () {
            // إعدادات البنوك
            Route::resource('banks_settings', 'banks_settingscontroller');
            // برامج البنوك
            Route::resource('banks_programs', 'bank_programscontroller');

            Route::get('/finance_banks', 'banks_settingscontroller@finance_banks')->name('finance_banks');
            Route::get('/finance_bank_branches', 'banks_settingscontroller@finance_bank_branches')->name('finance_bank_branches');
            Route::get('/finance_bank_accounts', 'banks_settingscontroller@finance_bank_accounts')->name('finance_bank_accounts');
            Route::get('/finance_currencies', 'banks_settingscontroller@finance_currencies')->name('finance_currencies');
            Route::get('/finance_bank_permissions', 'banks_settingscontroller@finance_bank_permissions')->name('finance_bank_permissions');
            Route::get('/finance_bank_thresholds', 'banks_settingscontroller@finance_bank_thresholds')->name('finance_bank_thresholds');
            Route::get('/finance_bank_gl_integration', 'banks_settingscontroller@finance_bank_gl_integration')->name('finance_bank_gl_integration');
            Route::get('/finance_default_currency', 'banks_settingscontroller@finance_default_currency')->name('finance_default_currency');


  // دورة الشيكات
      Route::get('/ChequeBooks', 'bank_programscontroller@ChequeBooks')->name('finance_bank_cheques.ChequeBooks');

    Route::get('/cheques/issue', 'bank_programscontroller@cheque_issue')->name('finance_bank_cheques.issue');
    Route::get('/cheques/approval', 'bank_programscontroller@cheque_approval')->name('finance_bank_cheques.approval');
    Route::get('/cheques/clearing', 'bank_programscontroller@cheque_clearing')->name('finance_bank_cheques.clearing');
    Route::get('/cheques/cancel', 'bank_programscontroller@cheque_cancel')->name('finance_bank_cheques.cancel');
    Route::get('/cheques/bounced', 'bank_programscontroller@cheque_bounced')->name('finance_bank_cheques.bounced');
    Route::get('/cheques/reconciliation', 'bank_programscontroller@cheque_reconciliation')->name('finance_bank_cheques.reconciliation');

    // الحركات البنكية الأخرى
    Route::get('/bank_transfers', 'bank_programscontroller@bank_transfers')->name('finance_bank_transfers');
    Route::get('/bank_movements', 'bank_programscontroller@bank_movements')->name('finance_bank_movements');
    Route::get('/bank_payments', 'bank_programscontroller@bank_payments')->name('finance_bank_payments');
    Route::get('/bank_receipts', 'bank_programscontroller@bank_receipts')->name('finance_bank_receipts');

    // التكامل مع الموديولات الأخرى
    Route::get('/bank_payroll', 'bank_programscontroller@bank_payroll')->name('finance_bank_payroll');
    Route::get('/bank_suppliers', 'bank_programscontroller@bank_suppliers')->name('finance_bank_suppliers');
    Route::get('/bank_patients_insurance', 'bank_programscontroller@bank_patients_insurance')->name('finance_bank_patients_insurance');
    Route::get('/bank_dashboard', 'bank_programscontroller@bank_dashboard')->name('finance.banks.dashboard');

            
        });
    },
);

Auth::routes();
// Auth::routes(['register' => false]); // لو عاوز تمنع التسجيل من الواجهة