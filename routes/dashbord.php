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
        Route::group(['namespace' => 'dashbord'], function () {
            Route::resource('dashbord', 'dashbordController');
        });
        Route::get('/{page}', 'AdminController@index');
    },
);

Auth::routes();
//Auth::routes(['register' => false]);