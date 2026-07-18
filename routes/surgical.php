<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\surgical\SurgicalBookingExportController;
use App\Http\Controllers\administrative\surgical\timeslotcontroller;
Auth::routes(['verify' => true]);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth', 'verified'],
    ],
    function () {
        //surgical
        Route::group(['namespace' => 'administrative\surgical'], function () {
            Route::resource('surgical_settings', 'surgical_settingscontroller');
            Route::resource('surgical_programs', 'surgical_programscontroller');
            Route::resource('surgical_booking', 'surgical_bookingcontroller');
            Route::resource('surgical_booking_list', 'surgical_bookingcontroller');
            Route::get('/surgical-report/print/{id}', 'SurgicalReportPrintController@print')->name('surgical_report.print');
            Route::resource('surgical_nursing', 'surgical_nursingcontroller');
            Route::resource('surgical_anesthesia', 'surgical_anesthesiacontroller');
            Route::resource('surgical_doctor', 'surgical_doctorcontroller');
            Route::resource('surgical_manager', 'surgical_managercontroller');
            Route::get('/surgical-booking/export-excel', [SurgicalBookingExportController::class, 'exportExcel'])->name('surgical_booking.export_excel');

            Route::resource('surgicaldatasettings', 'surgicaldatasettingscontroller');
            Route::get('/copy_surgical_tools', 'surgicaldatasettingscontroller@copy_surgical_tools')->name('get.copy_surgical_tools');

            Route::resource('scheduling_operation_room', 'roomsurgicalsettingscontroller');
            Route::resource('scheduling_operation', 'operationschedulecontroller');
            Route::get('scheduling_operation/{id}/index', 'operationschedulecontroller@index')->name('scheduling_operation.index');
            Route::get('scheduling_operation/{id}/create', 'operationschedulecontroller@create')->name('scheduling_operation.create');
            Route::get('scheduling_operation/{id}/create', 'operationschedulecontroller@create')->name('scheduling_operation.create');
            Route::post('/get-time-slots-surgical', 'timeslotcontroller@getTimeSlots')->name('get-time-slots-surgical');
            Route::get('/surgical-booking/{id}/details', [App\Http\Controllers\administrative\surgical\surgical_bookingcontroller::class, 'details'])->name('surgical_booking.details');
            Route::get('/surgical-nursing/{id}/details', [App\Http\Controllers\administrative\surgical\surgical_nursingcontroller::class, 'details'])->name('surgical_nursing.details');
            Route::get('/surgical-anesthesia/{id}/details', [App\Http\Controllers\administrative\surgical\surgical_anesthesiacontroller::class, 'details'])->name('surgical_anesthesia.details');
            Route::get('/surgical-doctor/{id}/details', [App\Http\Controllers\administrative\surgical\surgical_doctorcontroller::class, 'details'])->name('surgical_doctor.details');
            Route::get('/surgical_manger/{id}/details', [App\Http\Controllers\administrative\surgical\surgical_managercontroller::class, 'details'])->name('surgical_manger.details');
            Route::get('/surgical_financial_settings', 'surgicalfinancialsettingscontroller@index')->name('surgical_financial_settings');
            Route::get('/surgeryserviceitem/{id}', 'surgicalfinancialsettingscontroller@surgeryserviceitem')->name('surgeryserviceitem');

            Route::get('/surgical-booking/{id}/edit', 'surgical_bookingcontroller@edit')->name('surgical_booking.edit');

            Route::get('surgicaldatasettings/{id}/create', 'surgicaldatasettingscontroller@create')->name('surgicaldatasettings.create');
            Route::get('/get-scheduling_operation', 'operationschedulecontroller@getschedule')->name('get.scheduling_operation');
            Route::get('/getsurgical_booking', 'surgical_bookingcontroller@getbookings')->name('getsurgical_booking');
            Route::get('/surgical/time-slots', [timeslotcontroller::class, 'getTimeSlots'])->name('surgical.timeslots.get');
        });
        Route::group(['namespace' => 'general'], function () {
            //roomsurgicalsettings
            Route::resource('roomsurgicalsettings', 'roomsurgicalsettingscontroller');
            Route::get('roomsurgicalsettings/{id}/edit', 'roomsurgicalsettingscontroller@edit')->name('roomsurgicalsettings.edit');
            Route::delete('roomsurgicalsettings/{id}', 'roomsurgicalsettingscontroller@destroy')->name('roomsurgicalsettings.destroy');
        });
        Route::group(['namespace' => 'administrative\medical_record'], function () {
            // صفحة إنشاء المريض (فيو عادي)
            Route::get('/medical_record_add_s/create', 'new_patient_livewere@create')->name('medical_record_add_s.create');
            Route::post('/medical_record_add_s/store', 'new_patient_livewere@store')->name('medical_record_add_s.store');

            // مسارات العرض/التعديل/الحذف اختياريًا كما هي عندك
            Route::get('/new_patient_livewere', 'new_patient_livewere@index')->name('new_patient_livewere.index');

            // صفحة الحجز (تأكد أن عندك الراوت ده موجود بالفعل)
            Route::get('/surgical-booking', function () {
                return view('administrative/surgical/program/booking/create'); // الفيو اللي بيضم <livewire:surgery.create-booking />
            })->name('surgical_booking.index');
        });
    },
);

Auth::routes();