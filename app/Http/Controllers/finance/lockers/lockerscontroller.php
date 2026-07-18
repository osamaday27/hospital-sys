<?php
namespace App\Http\Controllers\finance\lockers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\models\finance\invoices;
use App\models\finance\receipts;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\calculateage;
use App\Traits\web\get_price_service;
use App\Traits\web\get_contract;
class lockerscontroller extends Controller
{
    use calculateage, get_price_service, get_contract;
     
   public function unpaid_receipts(Request $request)
    {
        $todayDate = now()->toDateString();
         $invoice = invoices::with('patient', 'reservations');
        $invoice = $invoice->get(); // استعلام للحصول على البيانات

        return DataTables::of($invoice)
            ->addColumn('sr_no', function ($invoice) {
                return $invoice->number;
            })
           
            ->addColumn('status', function ($invoice) {
                // حالة الحجز
                $statusClass = [
                    'unpaid' => 'warning',
                    'cancelled' => 'danger',
                    'paid' => 'success',
                   
                ];
                $status = $invoice->status;
                $statusClassBadge = $statusClass[$status] ?? 'light';
                $statusBadge = '<span class="badge bg-' . $statusClassBadge . '" style="display: block;margin-bottom: 10px;">' . trans('outpatient_trans.' . $status) . '</span>';

              

                // دمج الحالة ونوع الزيارة معًا
                return $statusBadge;
            })
          
            ->addColumn('patient_id', function ($invoice) {
                return $invoice->patient ? $invoice->patient->medical_number : '-';
            })
            ->addColumn('patient_name', function ($invoice) {
                return $invoice->patient ? $invoice->patient->full_name : '-';
            })
            ->addColumn('patient_phone', function ($invoice) {
                $phone = $invoice->patient->phone_number;
                $phone2 = $invoice->patient->phone_number2;
                $whatsapp = $invoice->patient->whatsapp;

                $phoneHtml = '<i class="bx bx-phone-outgoing text-primary"></i>' . $phone;

                if ($phone2) {
                    $phoneHtml .=
                        '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bx-phone-outgoing text-info" style="color: #140cfd;"></i>' .
                        $phone2 .
                        '</span>';
                }

                if ($whatsapp) {
                    $phoneHtml .=
                        '<span style="display: block; font-size: 12px; color: #888;">
                        <i class="bx bxl-whatsapp text-success"></i>' .
                        $whatsapp .
                        '</span>';
                }

                return $phoneHtml;
            })
            ->addColumn('age', function ($invoice) {
                return $invoice->patient ? Carbon::parse($invoice->patient->date_of_birth)->age : '-';
            })
            ->addColumn('patient_contract', function ($invoice) {
                return $invoice->patient && $this->getcontractname($invoice->patient->insurance_contracting) ? $this->getcontractname($invoice->patient->insurance_contracting) : '-';
            })
       
            ->addColumn('action', function ($data) {
                $encryptedPatientId = Crypt::encryptString($data->patient->id ?? '');
                $encryptedId = Crypt::encryptString($data->id);

                $actions =
                    '<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-cog-outline"></i> ' .
                    trans('outpatient_trans.action') .
                    '
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">';

                // Update patient data link
                $actions .=
                    '<a target="_blank"  class="dropdown-item" href="' .
                    route('medical_record.edit', ['medical_record' => $encryptedPatientId]) .
                    '">
                    <i class="ri-pencil-fill text-warning"></i> ' .
                    trans('outpatient_trans.update_patient_data') .
                    '
                </a>';

                // Add service button
                $actions .=
                    '<button class="dropdown-item" onclick="getsecond_level_service(); getdataresrv(' .
                    $data->contract .
                    ', ' .
                    $data->id_patient .
                    ', ' .
                    $data->id .
                    ');" data-bs-toggle="modal" data-bs-target="#add_service">
                <i class="mdi mdi-book-cross text-primary"></i> ' .
                    trans('outpatient_trans.add_service') .
                    '</button>';

                // Cancel invoice button
                $actions .=
                    '<button class="dropdown-item" onclick="cancel_invoice(' .
                    $data->id .
                    ')" data-bs-toggle="modal" data-bs-target="#cancelReservationModal">
                    <i class="mdi mdi-table-cancel text-danger"></i> ' .
                    trans('outpatient_trans.cancel_invoice') .
                    '
                </button>';

                // Visit history button
                $actions .=
                    '<button class="dropdown-item" onclick="get_last_visit(' .
                    $data->id_patient .
                    ')" data-bs-toggle="modal" data-bs-target="#visitModal">
                    <i class="mdi mdi-book-clock text-info"></i> ' .
                    trans('outpatient_trans.visits') .
                    '
                </button>';

                $actions .= '<div class="dropdown-divider"></div>';

                // Attachments link
                $actions .=
                    '<a class="dropdown-item" href="">
                    <i class="mdi mdi-folder-check-outline text-success"></i> ' .
                    trans('outpatient_trans.attachments') .
                    '
                </a>';

                // Print receipt link
                $actions .=
                    '<a class="dropdown-item" target="_blank" href="">
                    <i class="mdi mdi-printer text-secondary"></i> ' .
                    trans('outpatient_trans.print_receipt') .
                    '
                </a>';

                // Print prescription link
                $actions .=
                    '<a class="dropdown-item" target="_blank" href="">
                    <i class="mdi mdi-printer-outline text-secondary"></i> ' .
                    trans('outpatient_trans.print_prescription') .
                    '
                </a>';

                $actions .= '</div></div>';

                return $actions;
            })
            ->editColumn('created_at', function ($invoice) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($invoice->created_at)->format('d/m/Y h:i A');
            })
            ->rawColumns(['status', 'action',  'patient_phone']) // معاملة الأعمدة الخاصة بالـ status و action كـ raw
            ->make(true);
    }    
}