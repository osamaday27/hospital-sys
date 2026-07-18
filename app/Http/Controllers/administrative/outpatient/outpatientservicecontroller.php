<?php
namespace App\Http\Controllers\administrative\outpatient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\administrative\reservations;
use App\models\administrative\patient_visits;
use App\models\finance\invoices;
use App\models\finance\receipts;
use App\models\finance\finservicevisit;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
class outpatientservicecontroller extends Controller
{
    public function save_service_data(Request $request)
    {
        // Validate the request data
        $data = $request->validate([
            'services' => 'required|array',
            'services.*.service_id' => 'required|integer',
            'services.*.total' => 'required|numeric',
            'services.*.contracting_value' => 'required|numeric',
            'services.*.patient_value' => 'required|numeric',
            'services.*.discount' => 'required|numeric',
            'services.*.quantity' => 'required|integer',
            'reservation_id' => 'required|integer',
            'patient_id' => 'required|integer',
        ]);

        // Get the invoice and reservation details
        $invoice = invoices::where('reservation_id', $request->reservation_id)->first();
        $reservations = reservations::find($request->reservation_id);

        // Initialize totals for invoice update
        $totalPricing = 0;
        $contractContribution = 0;
        $patientContribution = 0;

        // Save service data and update totals
        foreach ($data['services'] as $service) {
            // Save service data
            finservicevisit::create([
                'id_invoices' => $invoice->id,
                'id_visit' => $reservations->visit_id,
                'id_services' => $service['service_id'],
                'total_pricing' => $service['total'],
                'contract_contribution' => $service['contracting_value'],
                'patient_contribution' => $service['patient_value'],
                'discount' => $service['discount'],
                'currency' => 'EGP',
                'quantity' => $service['quantity'],
                'id_patient' => $data['patient_id'],
            ]);
        }

        $remaining_amount = $request->input('unpaid_service');
        $amount_paid = $request->input('paid_service');
        $amount_required = $request->input('patient_value_service');

        $totalinvoice = $invoice->total;
        $contract_contributioninvoice = $invoice->contract_contribution;
        $patient_contributioninvoice = $invoice->patient_contribution;
        $discountioninvoice = $invoice->discount;
        $residualioninvoice = $invoice->residual;
        $paidioninvoice     = $invoice->paid;

        // Update the invoice totals
        $invoice->total = $totalinvoice + $request->input('total_service');
        $invoice->contract_contribution = $contract_contributioninvoice + $request->contracting_value_service;
        $invoice->patient_contribution = $patient_contributioninvoice + $request->patient_value_service;
        $invoice->discount = $discountioninvoice + $request->discount_service;
        $invoice->residual = $residualioninvoice + $request->unpaid_service;
        $invoice->paid     = $paidioninvoice + $request->paid_service;
        if($patient_contributioninvoice + $request->patient_value_service==$paidioninvoice + $request->paid_service)
        {
            $invoice->status='unpaid';
        }
        $invoice->save();
        // إنشاء إيصال جديد إذا كانت هناك دفعات
        if ($remaining_amount != $amount_required || $remaining_amount == 0) {
            $receipts = new receipts();
            $receipts->invoices_number = $invoice->number;
            $receipts->invoices_id = $invoice->id;
            $receipts->id_reservation = $invoice->reservation_id;
            $receipts->pay_type = 'cash';
            $receipts->id_contract = $reservations->contract;
            $receipts->total_paid = $amount_paid;
            $receipts->total_remainder = $remaining_amount;
            $receipts->id_patient = $data['patient_id'];
            $receipts->treasury = 'main';
            $receipts->notes = null;
            $receipts->branches = '1';
            $receipts->id_user = auth()->id();
            $receipts->save();
        }
        return response()->json(['success' => true]);
    }
   public function getServiceList(Request $request)
{
    if ($request->ajax()) {
        $reservations = reservations::find($request->reservation_id);
        $services = finservicevisit::with('service')->where('id_visit',$reservations->visit_id)->get(); // تأكد من وجود العلاقة الصحيحة
        return DataTables::of($services)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-danger btn-sm" onclick="deleteFunctionservis(' . $row->id . ')">'.trans('buttons.delete').'</button>';
            })
            ->editColumn('service_name', function ($row) {
                return $row->service->name ?? 'N/A'; // استخدم الحقل الصحيح لاسم الخدمة
            })
            ->editColumn('category', function ($row) {
                return $row->service->sublevel->name ?? 'N/A'; // مثال على الوصول إلى التصنيف من خلال العلاقة
            })
            ->make(true);
    }
    return response()->json(['error' => 'Invalid request'], 400);
}
 // Handle delete request
    public function destroy($id)
    {
        $service = finservicevisit::find($id);
        if ($service) {
            $service->delete();
            return response()->json(['success' => 'Service record deleted successfully']);
        }
        return response()->json(['error' => 'Service not found'], 404);
    }
    public function add_services($reservation_id) {
       $reservations=$reservation_id;
        return view('medical.nursing.program.add_services', compact('reservations'));
    }


}