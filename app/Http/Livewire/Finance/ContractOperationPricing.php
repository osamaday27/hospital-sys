<?php

namespace App\Http\Livewire\Finance;

use Livewire\Component;
use App\Models\Finance\operation_contract_pricing;
use App\Models\Finance\contract_last_level;
use App\Models\Finance\service;
use Illuminate\Support\Facades\Auth;
use App\Traits\Web\get_price_service_org;

class ContractOperationPricing extends Component
{
    use get_price_service_org;

    public $contract_id;
    public $surgery_id;
    public $contract;
    public $surgeries = [];

    public $surgeon_fee = 0;
    public $assistant_fee = 0;
    public $anesthesia_fee = 0;
    public $anesthesia_type;
    public $nursing_fee = 0;
    public $operation_room_fee = 0;
    public $additional_time_fee = 0;
    public $supplies_cost = 0;
    public $other_services = 0;
    public $discount = 0;
    public $vat = 0;
    public $total_price = 0;

    public function mount($contract_id, $surgery_id)
    {
        $this->contract_id = $contract_id;
        $this->surgery_id  = $surgery_id;
        $this->contract    = contract_last_level::findOrFail($this->contract_id);

        $this->surgeries   = service::where('sublevel_id', 7)->get();

        // تحميل بيانات العملية من التعاقد بشكل تلقائي
        $this->loadPriceFromContract();
    }

    private function normalizeNumber($value): float
    {
        if ($value === null) return 0.0;
        if (is_string($value)) {
            $v = trim($value);
            if ($v === '' || $v === '0') return 0.0;
            $v = str_replace([' ', '٬', ','], '', $v);
            $v = str_replace(['٫', '،'], '.', $v);
            $v = preg_replace('/[^0-9\.\-]/', '', $v) ?: '0';
            return (float) $v;
        }
        return (float) $value;
    }

    public function loadPriceFromContract()
    {
        if (!$this->surgery_id || !$this->contract_id) {
            session()->flash('error', 'يجب اختيار العقد والعملية أولاً');
            return;
        }

        // التحقق هل العملية لها تسعير مسبقًا
        $pricing = operation_contract_pricing::where('contract_id', $this->contract_id)
            ->where('surgery_id', $this->surgery_id)
            ->first();

        if ($pricing) {
            // تحميل البيانات القديمة
            $this->surgeon_fee         = $this->normalizeNumber($pricing->surgeon_fee);
            $this->assistant_fee       = $this->normalizeNumber($pricing->assistant_fee);
            $this->anesthesia_fee      = $this->normalizeNumber($pricing->anesthesia_fee);
            $this->anesthesia_type     = $pricing->anesthesia_type;
            $this->nursing_fee         = $this->normalizeNumber($pricing->nursing_fee);
            $this->operation_room_fee  = $this->normalizeNumber($pricing->operation_room_fee);
            $this->additional_time_fee = $this->normalizeNumber($pricing->additional_time_fee);
            $this->supplies_cost       = $this->normalizeNumber($pricing->supplies_cost);
            $this->other_services      = $this->normalizeNumber($pricing->other_services);
            $this->discount            = $this->normalizeNumber($pricing->discount);
            $this->vat                 = $this->normalizeNumber($pricing->vat);
            $this->total_price         = $this->normalizeNumber($pricing->total_price);
        } else {
            // تحميل السعر الافتراضي من التعاقد
            $result = $this->getPrice($this->contract_id, $this->surgery_id);

            if (is_array($result)) {
                $this->total_price = $this->normalizeNumber($result['hospital_price'] ?? 0);
            } else {
                session()->flash('error', $result);
            }
        }
    }

    public function save()
    {
        $this->normalizeAll();

        if (!$this->contract_id || !$this->surgery_id) {
            session()->flash('error', 'يجب اختيار العقد والعملية أولاً.');
            return;
        }

        $total_details =
            $this->surgeon_fee +
            $this->assistant_fee +
            $this->anesthesia_fee +
            $this->nursing_fee +
            $this->operation_room_fee +
            $this->additional_time_fee +
            $this->supplies_cost +
            $this->other_services;

        $total_details -= $this->discount;
        $total_details += $this->vat;

        if ($this->total_price == 0 && $total_details > 0) {
            $this->total_price = $total_details;
        }

        if ($this->total_price < $total_details) {
            session()->flash('error', 'إجمالي التفاصيل يتجاوز السعر الكلي للعملية.');
            return;
        }

        operation_contract_pricing::updateOrCreate(
            [
                'contract_id' => $this->contract_id,
                'surgery_id'  => $this->surgery_id,
            ],
            [
                'surgeon_fee'         => $this->surgeon_fee,
                'assistant_fee'       => $this->assistant_fee,
                'anesthesia_fee'      => $this->anesthesia_fee,
                'anesthesia_type'     => $this->anesthesia_type,
                'nursing_fee'         => $this->nursing_fee,
                'operation_room_fee'  => $this->operation_room_fee,
                'additional_time_fee' => $this->additional_time_fee,
                'supplies_cost'       => $this->supplies_cost,
                'other_services'      => $this->other_services,
                'discount'            => $this->discount,
                'vat'                 => $this->vat,
                'total_price'         => $this->total_price,
                'status'              => 'active',
                'created_by'          => Auth::id(),
                'updated_by'          => Auth::id(),
            ]
        );

        session()->flash('success', 'تم حفظ تسعير العملية بنجاح.');
    }

    private function normalizeAll()
    {
        $this->surgeon_fee         = $this->normalizeNumber($this->surgeon_fee);
        $this->assistant_fee       = $this->normalizeNumber($this->assistant_fee);
        $this->anesthesia_fee      = $this->normalizeNumber($this->anesthesia_fee);
        $this->nursing_fee         = $this->normalizeNumber($this->nursing_fee);
        $this->operation_room_fee  = $this->normalizeNumber($this->operation_room_fee);
        $this->additional_time_fee = $this->normalizeNumber($this->additional_time_fee);
        $this->supplies_cost       = $this->normalizeNumber($this->supplies_cost);
        $this->other_services      = $this->normalizeNumber($this->other_services);
        $this->discount            = $this->normalizeNumber($this->discount);
        $this->vat                 = $this->normalizeNumber($this->vat);
        $this->total_price         = $this->normalizeNumber($this->total_price);
    }

    public function render()
    {
        return view('livewire.finance.contract-operation-pricing', [
            'contract' => $this->contract,
        ]);
    }
}