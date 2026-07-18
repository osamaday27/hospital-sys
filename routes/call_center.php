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
        Route::group(['namespace' => 'administrative\call_center'], function () {
            Route::resource('call_center_all_programs', 'all_programscontroller');
            Route::resource('call_center_program', 'call_center_programscontroller');
            Route::resource('call_center_settings', 'call_center_settingscontroller');
            //answers types
            Route::resource('answer_types', 'answertypecontroller');
            //answers values
            Route::resource('answer_values', 'answervaluecontroller');
            //questionnaire
            Route::resource('questionnaires', 'questionnairecontroller');
            //questions
            Route::resource('questions', 'questioncontroller');
            //all_questionnaires
            Route::resource('all_questionnaires', 'call_center_all_questionnairecontroller');
            Route::get('/check-questionnaire', 'call_center_all_questionnairecontroller@checkQuestionnaire')->name('checkQuestionnaire');
            Route::get('/get-questions', 'call_center_all_questionnairecontroller@getQuestions')->name('getQuestions');
            Route::get('/create_questionnaires', 'call_center_all_questionnairecontroller@create_questionnaires')->name('create_questionnaires');
            Route::get('/get_all_questionnaires', 'call_center_all_questionnairecontroller@getquestionnaires')->name('get_all_questionnaires');
            Route::get('/getquestionnaires', 'call_center_all_questionnairecontroller@getquestionnaires')->name('getquestionnaires');
            Route::post('/updateQuestionnaireStatus{id}', 'call_center_all_questionnairecontroller@updateQuestionnaireStatus')->name('updateQuestionnaireStatus');
            Route::get('/upload-attachments/{id}', 'call_center_all_questionnairecontroller@uploadAttachments')->name('uploadAttachments');
            Route::post('/upload-attachments/{id}', 'call_center_all_questionnairecontroller@storeAttachments')->name('uploadAttachments.store');
            Route::post('/delete-attachment', 'call_center_all_questionnairecontroller@deleteAttachment')->name('deleteAttachment');
            //daily_questionnaires
            Route::get('/daily_questionnaires', 'call_center_all_questionnairecontroller@daily_questionnaires')->name('daily_questionnaires');
            Route::get('get-todays-visits', 'call_center_all_questionnairecontroller@getTodaysVisits')->name('get.todays.visits');
            // Route::get('get-todays-visits', [CallCenterAllQuestionnaireController::class, 'getTodaysVisits'])
            // ->name('get.todays.visits');
            //complaints-category
            Route::resource('complaints-category', 'complaints_categorycontroller');
            //complaints-problem
            Route::resource('complaints-problem', 'complaints_problemcontroller');
            //complaints-responsible
            Route::resource('complaints-responsible', 'complaints_responsiblecontroller');
            //complaints
            Route::resource('complaints', 'complaintscontroller');
            Route::get('/choose-patient', 'complaintscontroller@choosePatient')->name('choose-Patient');
            Route::get('/create-complaints', 'complaintscontroller@createComplaints')->name('create-complaints');
            Route::get('/get-complaints', 'complaintscontroller@getComplaints')->name('get-complaints');
            Route::post('/addAction/{id}', 'complaintscontroller@addAction')->name('complaints.addAction');
            Route::post('/deleteAttachment', 'complaintscontroller@deleteAttachment')->name('complaints.deleteAttachment');

            Route::post('/save-reservation_call_center', 'call_center_programscontroller@store')->name('save.reservation_call_center');

            //patient_inquiries
            Route::get('patient_inquiries/datatable', 'patient_inquiriescontroller@datatable')->name('patient_inquiries.datatable');
            Route::patch('patient_inquiries/{id}/review', 'patient_inquiriescontroller@review')->name('patient_inquiries.review');
            Route::get('patient_inquiries/{id}/modal', 'patient_inquiriescontroller@modal')->name('patient_inquiries.modal');
            Route::get('patient_inquiries/ajax/patients', 'patient_inquiriescontroller@ajaxPatients')->name('patient_inquiries.ajax.patients');
            Route::get('patient_inquiries/ajax/doctors', 'patient_inquiriescontroller@ajaxDoctors')->name('patient_inquiries.ajax.doctors');

            Route::resource('patient_inquiries', 'patient_inquiriescontroller')->except([]); // ممكن ترجع show دلوقتي
            Route::get('/absence_reasons', 'call_center_settingscontroller@absence_reasons')->name('absence_reasons');
            Route::get('/no_show_reservations', 'call_center_programscontroller@no_show_reservations')->name('no_show_reservations');


        });
    },
);

Auth::routes();
//Auth::routes(['register' => false]);