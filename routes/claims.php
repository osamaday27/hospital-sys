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
        Route::group(['namespace' => 'finance\claims'], function () {
            Route::get('/insurance_claims_index', 'claims_programscontroller@claims_manager')->name('insurance_claims_index');
            Route::get('/claims_create', 'claims_programscontroller@claims_create')->name('claims_create');
        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);