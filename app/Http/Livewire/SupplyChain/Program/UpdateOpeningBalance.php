<?php
namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\StoreMovement;
use App\Models\supply_chain\productdata;
use App\Models\supply_chain\unit;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\productstorebalance;
use App\Models\suppliers\suppliersdata;
use App\Models\supply_chain\storemaindata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateOpeningBalance extends Component
{
    public $movement_number;
    public $supplier_id, $store_id, $movement_date, $reference_no, $invoice_no, $notes;
    public $items = [];

    public function mount($movement_number)
    {
        $this->movement_number = $movement_number;

        $firstItem = StoreMovement::where('movement_number', $movement_number)->firstOrFail();

        $this->supplier_id = $firstItem->supplier_id;
        $this->store_id = $firstItem->store_id;
        $this->movement_date = $firstItem->movement_date;
        $this->reference_no = $firstItem->reference_no;
        $this->invoice_no = $firstItem->invoice_no;
        $this->notes = $firstItem->notes;

        $records = StoreMovement::where('movement_number', $movement_number)->get();

        foreach ($records as $record) {
            $unitInfo = productunit::where('product_id', $record->product_id)
                ->where(function ($q) use ($record) {
                    $q->where('unit_small_id', $record->units_id)->orWhere('unit_large_id', $record->units_id);
                })
                ->first();

            $relatedUnits = [];
            if ($unitInfo) {
                if ($unitInfo->unit_large_id) $relatedUnits[] = unit::find($unitInfo->unit_large_id);
                if ($unitInfo->unit_small_id) $relatedUnits[] = unit::find($unitInfo->unit_small_id);
            }

            $this->items[] = [
                'product_id' => $record->product_id,
                'unit_id' => $record->units_id,
                'quantity' => $record->quantity,
                'expiry_date' => $record->expiry_date,
                'batch_no' => $record->batch_no,
                'cost_price' => $unitInfo->cost_price ?? 0,
                'sale_price' => $unitInfo->sale_price ?? 0,
                'code' => $record->product->product_code,
                'name' => $record->product->name_ar,
                'current_balance' => productstorebalance::where([
                    ['product_id', $record->product_id],
                    ['store_id', $record->store_id],
                    ['units_id', $record->units_id],
                ])->sum('quantity'),
                'related_units' => collect($relatedUnits)->unique('id')->values()->all(),
            ];
        }
    }

    public function update()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers_data,id',
            'store_id' => 'required|exists:store_main_date,id',
            'movement_date' => 'required|date',
            'items.*.product_id' => 'required|exists:products_data,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () {
            // حذف السجلات القديمة
            StoreMovement::where('movement_number', $this->movement_number)->delete();

            // إعادة إدخالها بعد التعديل
            foreach ($this->items as $item) {
                StoreMovement::create([
                    'movement_number' => $this->movement_number,
                    'product_id' => $item['product_id'],
                    'supplier_id' => $this->supplier_id,
                    'store_id' => $this->store_id,
                    'units_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'batch_no' => $item['batch_no'],
                    'expiry_date' => $item['expiry_date'],
                    'movement_type' => 'opening_balance',
                    'reference_no' => $this->reference_no,
                    'invoice_no' => $this->invoice_no,
                    'movement_date' => $this->movement_date,
                    'notes' => $this->notes,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        session()->flash('success', 'تم تعديل الحركة بنجاح');
    }

    public function render()
    {
        return view('livewire.supply-chain.program.update-opening-balance', [
            'suppliers' => suppliersdata::all(),
            'stores' => storemaindata::all(),
            'products' => productdata::all(),
            'units' => unit::all(),
        ]);
    }
}