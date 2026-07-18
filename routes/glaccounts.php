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
        //glaccounts
        Route::group(['namespace' => 'finance\glaccounts'], function () {
            Route::resource('glaccounts_settings', 'glaccounts_settingscontroller');
            Route::resource('glaccounts_programs', 'glaccounts_programscontroller');
            Route::get('/glaccounts', 'glaccounts_settingscontroller@glaccounts')->name('glaccounts');
            Route::get('/gl_entries', 'glaccounts_programscontroller@gl_entries')->name('gl_entries');
            Route::get('/trial_balance', 'glaccounts_programscontroller@trial_balance')->name('trial_balance');
            Route::get('/financial_statements', 'glaccounts_programscontroller@financial_statements')->name('financial_statements');
            Route::get('/gl_closing', 'glaccounts_programscontroller@gl_closing')->name('gl_closing');

            
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);