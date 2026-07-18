<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\models\Finance\Service;
use App\models\medical\doctor_order; // تحديث المودل الجديد
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use App\Traits\Finance\invoiceservicetrait;
use App\Traits\Web\get_price_service;
use App\models\administrative\reservations;

class DoctorOrderEye extends Component
{
    use WithPagination;
    use invoiceservicetrait;
    use get_price_service;
    public $patient_id;
    public $patient_visits_id;
    public $selectedLab = [];
    public $selectedRad = [];
    public $selectedEyeLab = [];
    public $selectedEyeRad = [];
    public $selectedexTests = [];
    public $selectedEyeexTests = [];
    public $searchEye = '';
    public $searchLab = '';
    public $searchRad = '';
    public function getFilteredEyeTestsProperty()
    {
        return Service::whereHas('sublevel.level1', function ($q) {
            $q->where('level_type', 'exTestseye');
        })
            ->when($this->searchEye, function ($query) {
                $query->where('name', 'like', '%' . $this->searchEye . '%');
            })
            ->paginate(10);
    }

    public function getFilteredLabTestsProperty()
    {
        return Service::whereHas('sublevel.level1', function ($q) {
            $q->where('level_type', 'laboratory');
        })
            ->when($this->searchLab, function ($query) {
                $query->where('name', 'like', '%' . $this->searchLab . '%');
            })
            ->paginate(10);
    }

    public function getFilteredRadTestsProperty()
    {
        return Service::whereHas('sublevel.level1', function ($q) {
            $q->where('level_type', 'radiology');
        })
            ->when($this->searchRad, function ($query) {
                $query->where('name', 'like', '%' . $this->searchRad . '%');
            })
            ->paginate(10);
    }

    public function toggleSelectAll($type)
    {
        switch ($type) {
            case 'eye':
                foreach ($this->filteredEyeTests as $i => $exam) {
                    $this->selectedexTests[$i] = $exam['id'];
                }
                break;
            case 'lab':
                foreach ($this->filteredLabTests as $i => $lab) {
                    $this->selectedLab[$i] = $lab['id'];
                }
                break;
            case 'rad':
                foreach ($this->filteredRadTests as $i => $rad) {
                    $this->selectedRad[$i] = $rad['id'];
                }
                break;
        }
    }

    public function mount($patientId, $visitId)
    {
        $this->patient_id = $patientId;
        $this->patient_visits_id = $visitId;
    }

    public function save()
    {
        if (empty($this->selectedLab) && empty($this->selectedRad) && empty($this->selectedexTests)) {
            session()->flash('error', 'يجب اختيار اختبار واحد على الأقل.');
            return;
        }

        // إنشاء سجل جديد للطلب لكل من المعمل والأشعة
        if (!empty($this->selectedLab)) {
            doctor_order::create([
                'id_patient' => $this->patient_id,
                'id_visit' => $this->patient_visits_id,
                'request_type' => 'lab',
                'service_id' => json_encode($this->prepareRequestData($this->selectedLab, $this->selectedEyeLab)),
                'request_date' => now(),
                'status' => 'pending',
                'user_id' => Auth::id(),
            ]);
        }

        if (!empty($this->selectedRad)) {
            doctor_order::create([
                'id_patient' => $this->patient_id,
                'id_visit' => $this->patient_visits_id,
                'request_type' => 'xray',
                'service_id' => json_encode($this->prepareRequestData($this->selectedRad, $this->selectedEyeRad)),
                'request_date' => now(),
                'status' => 'pending',
                'user_id' => Auth::id(),
            ]);
        }
        if (!empty($this->selectedexTests)) {
            // جلب سجل الحجز المرتبط
            $reservation = reservations::where('id_patient', $this->patient_id)->where('visit_id', $this->patient_visits_id)->firstOrFail();

            // إعداد بيانات الخدمات للفاتورة
            $servicesList = [];
            foreach ($this->selectedexTests as $index => $testId) {
                $priceDataRaw = $this->getPrice($reservation->contract, $testId);
                $priceData = is_string($priceDataRaw)
                    ? json_decode($priceDataRaw, true)
                    : (array) $priceDataRaw;
    
                $unitPrice    = $priceData['original_price'] ?? 0;
                $patientValue = $priceData['amount_to_pay'] ?? 0;
                $contractValue = round($unitPrice - $patientValue, 2);
                $totalPrice = round($unitPrice, 2);
                $servicesList[] = [
                    'service_id' => $testId,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'patient_value' => $patientValue,
                    'contract_value' => $contractValue,
                    'total_price' => $totalPrice,
                ];
            }
            // إضافة الخدمات وتحديث الفاتورة
            $this->addServicesToInvoice($reservation->id, $servicesList);

            doctor_order::create([
                'id_patient' => $this->patient_id,
                'id_visit' => $this->patient_visits_id,
                'request_type' => 'exTestseye',
                'service_id' => json_encode($this->prepareRequestData($this->selectedexTests, $this->selectedEyeexTests)),
                'request_date' => now(),
                'status' => 'pending',
                'user_id' => Auth::id(),
            ]);
        }
        session()->flash('success', 'تم حفظ الطلبات بنجاح!');
        $this->reset(['selectedLab', 'selectedRad', 'selectedEyeLab', 'selectedEyeRad', 'selectedexTests', 'selectedEyeexTests']);
    }

    /**
     * إعداد بيانات الطلب بحيث تحتوي على المعرف والعين المختارة
     */
    private function prepareRequestData($tests, $eyeSelections)
    {
        $requestData = [];
        foreach ($tests as $index => $testId) {
            $requestData[] = [
                'test_id' => $testId,
                'eye' => $eyeSelections[$index] ?? 'both',
            ];
        }
        return $requestData;
    }
    public function render()
    {
        return view('livewire.doctor-order-eye', [
            'filteredEyeTests' => $this->filteredEyeTests,
            'filteredLabTests' => $this->filteredLabTests,
            'filteredRadTests' => $this->filteredRadTests,
            'previousOrders' => doctor_order::where('id_patient', $this->patient_id)->where('id_visit', $this->patient_visits_id)->latest()->get(),
        ]);
    }
}