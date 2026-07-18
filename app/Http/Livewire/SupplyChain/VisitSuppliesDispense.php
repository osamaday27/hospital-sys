<?php

namespace App\Http\Livewire\SupplyChain;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\supply_chain\AdmBVisitSupply;
use App\Models\supply_chain\Productdata;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\productstorebalance;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\StoreMovement;
use App\Models\finance\invoices;
use App\Services\SupplyChain\ProductUnitService;

class VisitSuppliesDispense extends Component
{
    use WithPagination;

    // معرف الحجز والزيارات
    public $reservationId;
    public $patientId;
    public $visitId;

    // حقول الإدخال
    public $store_id;
    public $product_id;
    public $unit_id;
    public $quantity;
    public $price = 0;

    // بيانات القوائم والرصيد
    public $stores   = [];
    public $products = [];
    public $units    = [];
    public $balance  = 0;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'store_id'   => 'required|exists:store_main_date,id',
        'product_id' => 'required|exists:products_data,id',
        'unit_id'    => 'required',
        'quantity'   => 'required|numeric|min:0.01',
    ];

    public function mount($reservationId)
    {
        $this->reservationId = $reservationId;
        $reservation = \App\Models\administrative\reservations::findOrFail($reservationId);
        $this->patientId = $reservation->id_patient;
        $this->visitId   = $reservation->visit_id;

        $this->stores   = storemaindata::select('id','name_ar','manual_code')->get();
        $this->products = Productdata::select('id','name_ar')->get();
    }

    public function updatedProductId($value)
    {
        $this->resetInput();
        $this->loadUnits($value);
        $this->balance = $this->getCurrentBalance();
    }

    public function updatedStoreId()
    {
        $this->balance = $this->getCurrentBalance();
    }

    public function updatedUnitId($value)
    {
        $this->balance = $this->getCurrentBalance();
        $this->price   = $this->getSalePrice($this->product_id, $value);
    }

    protected function loadUnits($productId)
    {
        $this->units = productunit::with(['largeUnit','smallUnit'])
            ->where('product_id', $productId)
            ->get()
            ->flatMap(function($unit) {
                return collect([
                    $unit->largeUnit ? ['id'=>$unit->unit_large_id,'name_ar'=>$unit->largeUnit->name_ar,'name_en'=>$unit->largeUnit->name_en] : null,
                    $unit->smallUnit ? ['id'=>$unit->unit_small_id,'name_ar'=>$unit->smallUnit->name_ar,'name_en'=>$unit->smallUnit->name_en] : null,
                ])->filter()->all();
            })
            ->unique('id')
            ->values()
            ->toArray();
    }

    protected function getSalePrice(int $productId, int $unitId): float
    {
        $pu = productunit::where('product_id', $productId)
            ->where(function($q) use ($unitId) {
                $q->where('unit_large_id', $unitId)
                  ->orWhere('unit_small_id', $unitId);
            })->select('sale_price')->first();

        return $pu->sale_price ?? 0;
    }

    protected function getCurrentBalance(): float
    {
        if (!($this->store_id && $this->product_id && $this->unit_id)) {
            return 0;
        }

        $baseQty = productstorebalance::where('store_id', $this->store_id)
            ->where('product_id', $this->product_id)
            ->sum('quantity_in_base_unit');

        $rate = (new ProductUnitService)
            ->calculateSmallestUnits($this->product_id, $this->unit_id)['total_small_units'] ?? 1;

        return $rate > 0 ? round($baseQty / $rate, 2) : 0;
    }

    public function addSupply()
    {
        if ($this->quantity > $this->balance) {
            $this->addError('quantity', "الكمية المطلوبة أكبر من الرصيد المتاح ({$this->balance}).");
            return;
        }

        $this->validate();

        $unitRate       = (new ProductUnitService)
            ->calculateSmallestUnits($this->product_id, $this->unit_id)['total_small_units'] ?? 1;
        $qtyBase        = $this->quantity * $unitRate;
        $movementNumber = 'ISS-'.now()->format('YmdHis');

        DB::transaction(function() use ($qtyBase, $unitRate, $movementNumber) {
            $this->consumeBatches($qtyBase, $unitRate, $movementNumber);
            $this->recordSupply($movementNumber);
        });

        session()->flash('success', "تم الصرف بنجاح - رقم الحركة: {$movementNumber}");
        $this->reset(['product_id','unit_id','quantity','price','balance']);
    }

    protected function consumeBatches(float $qtyBase, float $unitRate, string $movementNumber)
    {
        $batches = productstorebalance::where('store_id', $this->store_id)
            ->where('product_id', $this->product_id)
            ->where('quantity_in_base_unit', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        foreach ($batches as $batch) {
            if ($qtyBase <= 0) break;

            $deductBase = min($qtyBase, $batch->quantity_in_base_unit);
            $deductUnit = $deductBase / $unitRate;

            StoreMovement::create([
                'movement_number' => $movementNumber,
                'product_id'      => $this->product_id,
                'store_id'        => $this->store_id,
                'units_id'        => $this->unit_id,
                'quantity'        => $deductUnit,
                'batch_no'        => $batch->batch_no,
                'expiry_date'     => $batch->expiry_date,
                'movement_type'   => 'withdrawal',
                'movement_date'   => now(),
                'reference_no'    => $movementNumber,
                'notes'           => 'صرف مستلزمات زيارة',
                'user_id'         => Auth::id(),
            ]);

            $batch->decrement('quantity_in_base_unit', $deductBase);
            $qtyBase -= $deductBase;
        }

        if ($qtyBase > 0) {
            throw new \Exception('الكمية المطلوبة أكبر من الرصيد المتاح.');
        }
    }

    protected function recordSupply(string $movementNumber)
    {
        $supply = AdmBVisitSupply::create([
            'reservation_id' => $this->reservationId,
            'patient_id'     => $this->patientId,
            'visit_id'       => $this->visitId,
            'store_id'       => $this->store_id,
            'product_id'     => $this->product_id,
            'unit_id'        => $this->unit_id,
            'quantity'       => $this->quantity,
            'price'          => $this->price,
            'created_by'     => Auth::id(),
        ]);

        // تحديث الفاتورة بإضافة مبلغ صرف المستلزمات
        if ($supply) {
            $amount = $this->quantity * $this->price;
            $invoice = invoices::firstOrNew(['reservation_id' => $this->reservationId]);
            $invoice->total    = ($invoice->total ?? 0) + $amount;
            $invoice->residual = ($invoice->residual ?? 0) + $amount;
            $invoice->save();
        }
    }

    public function removeSupply($id)
    {
        $item = AdmBVisitSupply::findOrFail($id);

        // إعادة الرصيد للمخزن
        $unitRate = (new ProductUnitService)
            ->calculateSmallestUnits($item->product_id, $item->unit_id)['total_small_units'] ?? 1;

        productstorebalance::where('store_id', $item->store_id)
            ->where('product_id', $item->product_id)
            ->where('units_id', $item->unit_id)
            ->increment('quantity_in_base_unit', $item->quantity * $unitRate);

        // خصم مبلغ الصرف من الفاتورة
        $amount = $item->quantity * $item->price;
        $invoice = invoices::where('reservation_id', $this->reservationId)->first();
        if ($invoice) {
            $invoice->total = max(0, $invoice->total - $amount);
            $invoice->residual = max(0, $invoice->residual - $amount);
            $invoice->save();
        }

        // حذف السجل
        $item->delete();
        session()->flash('success', 'تم حذف البند وإعادة الرصيد وتحديث الفاتورة.');
    }


    protected function resetInput()
    {
        $this->unit_id = null;
        $this->quantity = null;
        $this->price = 0;
        $this->balance = 0;
    }

    public function render()
    {
        $supplies = AdmBVisitSupply::where('reservation_id', $this->reservationId)
            ->where('patient_id', $this->patientId)
            ->where('visit_id', $this->visitId)
            ->latest()
            ->paginate(10);

        return view('livewire.supply-chain.visit-supplies-dispense', compact('supplies'));
    }
}