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
        //surgical
        Route::group(['namespace' => 'administrative\inquiries'], function () {
            Route::post('/doctor/schedule/search', 'inquiriescontroller@search')->name('doctor.schedule.search');
        });
    },
);

Auth::routes();