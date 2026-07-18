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
        //patient_accounts
        Route::group(['namespace' => 'finance\patient_accounts'], function () {
            Route::resource('patient_accounts_settings', 'patient_accounts_settingscontroller');
            Route::resource('patient_accounts_programs', 'patient_accounts_programscontroller');
            Route::get('/patient_accounts', 'patient_accounts_settingscontroller@patient_accounts')->name('patient_accounts');
            Route::get('/patient_advance_payments', 'patient_accounts_programscontroller@patient_advance_payments')->name('patient_advance_payments');
            Route::get('/patient_payments_history', 'patient_accounts_programscontroller@patient_payments_history')->name('patient_payments_history');
            Route::get('/patient_invoices', 'patient_accounts_programscontroller@patient_invoices')->name('patient_invoices');
            Route::get('/finance/patient_invoices_details/{invoices_details_id}', 'patient_accounts_programscontroller@patient_invoices_details')
            ->name('finance.patient_invoices_details');
            Route::get('/finance/advance-payment/print/{payment_id}', 'patient_accounts_programscontroller@printAdvancePayment')
            ->name('finance.advance_payment.print');
        
           
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);