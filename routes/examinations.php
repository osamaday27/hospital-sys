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
        //call_center
        Route::group(['namespace' => 'medical\examinations'], function () {
            Route::resource('examinations', 'examinationscontroller');
            Route::get('/get_examination_out', 'examinationscontroller@current_reservations')->name('get.examination_out');
            Route::get('start_session_examination/{id}/start', 'examinationscontroller@start')->name('start_session_examination.start');
            Route::post('/examination_test/end-session/{id}', 'examinationscontroller@endSession')->name('examination_test.end_session');

        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);