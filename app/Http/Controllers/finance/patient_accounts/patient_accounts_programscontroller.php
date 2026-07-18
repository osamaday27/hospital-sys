<?php
namespace App\Http\Controllers\finance\patient_accounts;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;
use App\Models\finance\AdvancePayment;
use App\models\administrative\adm_b_surgery_bookings;

class patient_accounts_programscontroller extends Controller
{
    use file_storage;

    public function index()
    {
        return view('finance.patient_accounts.program.index');
    }
  public function openAdvancePayment($id)
{
    // جلب بيانات الحجز
    $booking_info = \App\Models\administrative\adm_b_surgery_bookings::find($id);

    if (!$booking_info) {
        return redirect()->back()->with('error', 'لم يتم العثور على الحجز المطلوب.');
    }

    // تمرير بيانات الحجز للعرض
    return view('finance.patient_accounts.program.advancepayments', [
        'booking_info' => $booking_info
    ]);
}
  public function surgical_invoice($id)
{
    // جلب بيانات الحجز
    $booking_info = \App\Models\administrative\adm_b_surgery_bookings::find($id);

    if (!$booking_info) {
        return redirect()->back()->with('error', 'لم يتم العثور على الحجز المطلوب.');
    }

    // تمرير بيانات الحجز للعرض
    return view('finance.patient_accounts.program.surgical_invoice', [
        'booking_info' => $booking_info
    ]);
}
    public function patient_payments_history()
    {
        return view('finance.patient_accounts.program.payments_history');
    }
    public function patient_invoices()
    {
        return view('finance.patient_accounts.program.patient_invoices');
    }
    public function patient_invoices_details($invoices_details_id)
    {
        return view('finance.patient_accounts.program.patient_invoices_details',compact('invoices_details_id'));
    }
    public function printAdvancePayment($payment_id)
    {
        $payment = AdvancePayment::with(['patient', 'user', 'locker'])
            ->findOrFail($payment_id);

        return view('finance.patient_accounts.program.advance_payments_print', [
            'payment' => $payment,
        ]);
    }
    
}