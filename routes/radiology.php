<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\administrative\radiology\radiologyroomssettingscontroller;
use App\Http\Controllers\administrative\radiology\radiologydevicestypessettingscontroller;
use App\Http\Controllers\administrative\radiology\radiologydevicessettingscontroller;
use App\Http\Controllers\administrative\radiology\radiologymedicalservicessettingscontroller;
use App\Http\Controllers\administrative\radiology\radiologymedicalreportscontroller;
use App\Http\Controllers\administrative\radiology\radiologyfinancialsettingscontroller;
use App\Http\Controllers\administrative\radiology\radiologyquestionscontroller;
    use App\Http\Controllers\administrative\radiology\radiologypackagescontroller;




Auth::routes(['verify' => true]);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        //radiology
        Route::group(['namespace' => 'administrative\radiology'], function () {
            Route::resource('radiology_settings', 'radiology_settingscontroller');
            Route::resource('radiology_programs', 'radiology_programscontroller');

     Route::get('radiology_rooms_settings', [radiologyroomssettingscontroller::class, 'index'])
        ->name('radiology.rooms.settings.index');

   

// مجموعة الروت الخاصة بإعدادات الأشعة
    Route::get('radiology/devicestypes/settings', [radiologydevicestypessettingscontroller::class, 'index'])
        ->name('radiology.devicestypes.settings.index');

 Route::get('radiology/devices/settings', [radiologydevicessettingscontroller::class, 'index'])
                ->name('radiology.devices.settings.index');
// 🏥 الخدمات الطبية (Medical Services)
    Route::get('radiology/medical_services/settings', [radiologymedicalservicessettingscontroller::class, 'index'])
        ->name('radiology.medical_services.settings.index');

    Route::get('radiology/scheduling/settings', [\App\Http\Controllers\administrative\radiology\radiologyschedulingsettingscontroller::class, 'index'])
            ->name('radiology.scheduling.settings.index');

  
    Route::get('radiology/medicalreports/settings', [radiologymedicalreportscontroller::class, 'index'])
                ->name('medicalreports.index');

    Route::get('radiology/financial/settings', [radiologyfinancialsettingscontroller::class, 'index'])
        ->name('radiology.financial.settings.index');


    Route::get('radiology/questions/settings', [radiologyquestionscontroller::class, 'index'])
    ->name('radiology.questions.settings');



Route::get('radiology/settings/packages', [radiologypackagescontroller::class, 'index'])
    ->name('packages.index');

});


    },
);

Auth::routes();