<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\models\administrative\reservations;
use App\models\finance\invoices;
use App\models\finance\finservicevisit;
use App\models\finance\receipts;
use App\models\finance\servicesublevel;
use App\models\finance\service;
use App\Traits\Web\get_price_service;

class AddServices extends Component
{
    use get_price_service;

    public $reservationId;
    public $patientId;
    public $reservation;
    public $patient;
    public $sublevels = [];
    public $servicesOptions = [];

    // الخدمات المحفوظة مسبقاً (للعرض والحذف)
    public $existingServices = [];
    // الخدمات المضافة حاليًا (قبل الحفظ النهائي)
    public $servicesList = [];

    public $sublevel_id;
    public $service_id;
    public $quantity = 1;

    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;
        $this->servicesList = [];
        $this->loadReservation();
        $this->sublevels = servicesublevel::all();
    }

    public function loadReservation()
    {
        $this->reservation = reservations::with(['patient', 'clinic', 'doctor', 'service'])
                              ->find($this->reservationId);

        $this->patientId   = $this->reservation->patient_id;
        $this->patient     = $this->reservation->patient;

        $this->existingServices = finservicevisit::where('id_visit', $this->reservation->visit_id)
            ->with(['service.sublevel'])
            ->get()
            ->map(fn($item) => [
                'record_id'       => $item->id,
                'service_id'      => $item->service_id,
                'sublevel_name'   => $item->service->sublevel->name,
                'service_name'    => $item->service->name,
                'quantity'        => $item->quantity,
                'unit_price'      => $item->total_pricing / $item->quantity,
                'contract_value'  => $item->contract_contribution,
                'patient_value'   => $item->patient_contribution,
                'total_price'     => $item->total_pricing,
            ])->toArray();
    }

    public function updatedSublevelId($value)
    {
        $this->servicesOptions = service::where('sublevel_id', $value)->get();
    }

    public function addService()
    {
        $this->validate([
            'sublevel_id' => 'required',
            'service_id'  => 'required',
            'quantity'    => 'required|integer|min:1',
        ]);

        $priceData     = $this->getPrice($this->reservation->contract, $this->service_id);
        $unitPrice     = $priceData['original_price'];
        $patientValue  = $priceData['amount_to_pay'];
        $contractValue = round($unitPrice - $patientValue, 2);
        $totalPrice    = round($unitPrice * $this->quantity, 2);

        $sublevel = servicesublevel::find($this->sublevel_id);
        $service  = service::find($this->service_id);

        $this->servicesList[] = [
            'service_id'      => $service->id,
            'sublevel_name'   => $sublevel->name,
            'service_name'    => $service->name,
            'quantity'        => $this->quantity,
            'unit_price'      => $unitPrice,
            'contract_value'  => $contractValue,
            'patient_value'   => $patientValue,
            'total_price'     => $totalPrice,
        ];

        $this->service_id = null;
        $this->quantity   = 1;
    }

    public function saveServices()
    {
        if (empty($this->servicesList)) {
            session()->flash('error', 'لا توجد خدمات للحفظ.');
            return;
        }

        DB::transaction(function () {
            $invoice = invoices::where('reservation_id', $this->reservationId)->first();

            $sumTotal    = collect($this->servicesList)->sum('total_price');
            $sumPatient  = collect($this->servicesList)->sum('patient_value');
            $sumContract = collect($this->servicesList)->sum('contract_value');

            foreach ($this->servicesList as $row) {
                finservicevisit::create([
                    'id_invoices'           => $invoice->id,
                    'id_visit'              => $this->reservation->visit_id,
                    'id_services'           => $row['service_id'],
                    'quantity'              => $row['quantity'],
                    'total_pricing'         => $row['total_price'],
                    'contract_contribution' => $row['contract_value'],
                    'patient_contribution'  => $row['patient_value'],
                    'discount'              => $row['unit_price'] - $row['patient_value'],
                    'currency'              => 'EGP',
                    'id_patient'            => $this->patientId,
                ]);
            }

            $invoice->increment('total', $sumTotal);
            $invoice->increment('patient_contribution', $sumPatient);
            $invoice->increment('contract_contribution', $sumContract);
            $invoice->decrement('residual', $sumPatient);
            if ($invoice->status == 'paid') {
                $invoice->update(['status' => 'partial']);
            }
        });

        $this->servicesList = [];
        $this->loadReservation();

        session()->flash('success', 'تم حفظ الخدمات وتحديث الفاتورة بنجاح.');
    }

    public function removeExistingService($index)
    {
        $invoice = invoices::where('reservation_id', $this->reservationId)->first();

        if ($invoice->status == 'paid') {
            session()->flash('error', 'لا يمكن حذف خدمة من فاتورة مدفوعة.');
            return;
        }elseif ($invoice->status == 'partial') {
            session()->flash('error', 'لا يمكن حذف خدمة من فاتورة جزئية.');
            return;
        }else
        {
            if (!isset($this->existingServices[$index])) {
                session()->flash('error', 'الخدمة غير موجودة.');
                return;
            }
            $item = $this->existingServices[$index];
    
            DB::transaction(function () use ($item) {
                // حذف الخدمة من الجدول
                finservicevisit::destroy($item['record_id']);
    
                // تحديث الفاتورة بطرح قيمة الخدمة المحذوفة فقط
                $invoice = invoices::where('reservation_id', $this->reservationId)->first();
                $invoice->decrement('total', $item['total_price']);
                $invoice->decrement('contract_contribution', $item['contract_value']);
                $invoice->decrement('patient_contribution', $item['patient_value']);
                // المتبقي يزداد بقيمة ما دفعه المريض لهذه الخدمة
                $invoice->increment('residual', $item['patient_value']);
    
              
            });
    
            // إعادة تحميل الخدمات بعد التعديل
            $this->loadReservation();
        }
      
    }

    // قيم محسوبة للعرض
    public function getTotalPricingProperty()
    {
        return collect($this->existingServices)->sum('total_price')
             + collect($this->servicesList)->sum('total_price');
    }

    public function getTotalPatientContributionProperty()
    {
        return collect($this->existingServices)->sum('patient_value')
             + collect($this->servicesList)->sum('patient_value');
    }

    public function getTotalContractContributionProperty()
    {
        return collect($this->existingServices)->sum('contract_value')
             + collect($this->servicesList)->sum('contract_value');
    }

    public function render()
    {
        return view('livewire.medical.nursing.add-services');
    }
}