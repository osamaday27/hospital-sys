<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Auth routes
Auth::routes();
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return view('auth.login');
    });
});
// External route files
require base_path('routes/hr.php');
require base_path('routes/outpatient.php');
require base_path('routes/general.php');
require base_path('routes/medical_record.php');
require base_path('routes/finance.php');
require base_path('routes/locker.php');
require base_path('routes/glaccounts.php');
require base_path('routes/call_center.php');
require base_path('routes/inpatient.php');
require base_path('routes/suppliers.php');
require base_path('routes/supply_chain.php');
require base_path('routes/surgical.php');
require base_path('routes/doctor.php');
require base_path('routes/nursing.php');
require base_path('routes/inquiries.php');
require base_path('routes/medicalimage.php');
require base_path('routes/purchases.php');
require base_path('routes/assets.php');
require base_path('routes/patient_accounts.php');
require base_path('routes/examinations.php');
require base_path('routes/costing.php');
require base_path('routes/claims.php');
require base_path('routes/banks.php');
require base_path('routes/modules.php');
require base_path('routes/radiology.php');
require base_path('routes/dashbord.php');

// Reports routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/employee-acknowledgment', 'ReportController@employeeAcknowledgmentForm')->name('employee-acknowledgment.form');
        Route::post('/employee-acknowledgment/pdf', 'ReportController@generateEmployeeAcknowledgmentPdf')->name('employee-acknowledgment.pdf');
        Route::post('/employee-acknowledgment/excel', 'ReportController@generateEmployeeAcknowledgmentExcel')->name('employee-acknowledgment.excel');
        Route::get('/employee-acknowledgment/preview', 'ReportController@previewEmployeeAcknowledgment')->name('employee-acknowledgment.preview');
        Route::post('/employee-acknowledgment/bulk', 'ReportController@generateBulkEmployeeAcknowledgment')->name('employee-acknowledgment.bulk');
    });
    
});

// Acknowledgment generation routes
Route::post('/acknowledgments/generate', 'App\Http\Controllers\AcknowledgmentController@generate')->name('acknowledgments.generate');

// Catch-all route for admin pages (improve with regex or explicit names if needed)
Route::get('/{page}', 'AdminController@index');