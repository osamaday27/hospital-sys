<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\{InventoryAudit, InventoryAuditDetail, storemaindata, productdata, productstorebalance, unit};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateInventoryAudit extends Component
{
    public $store_id, $audit_date, $notes;
    public $products = [];
    public $storeProducts = [];

    public function mount()
    {
        $this->audit_date = now()->format('Y-m-d');
    }

    public function updatedStoreId()
    {
        $this->loadStoreProducts();
    }

    public function loadStoreProducts()
    {
        $this->storeProducts = productstorebalance::with(['store', 'unit', 'product'])
            ->where('store_id', $this->store_id)
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'units_id' => $item->units_id,
                    'system_quantity' => $item->quantity,
                    'actual_quantity' => null,
                    'difference_quantity' => 0,
                    'difference_type' => null,
                    'reason' => '',
                    'notes' => '',
                    'product_name' => $item->product->name_ar ?? '-',
                    'unit_name' => $item->unit->name_ar ?? '-',
                ];
            })->toArray();
    }

    public function save()
    {
        $this->validate([
            'store_id' => 'required',
            'audit_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $audit = InventoryAudit::create([
                'store_id' => $this->store_id,
                'audit_date' => $this->audit_date,
                'status' => 'draft',
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            foreach ($this->storeProducts as $detail) {
                $detail['difference_quantity'] = $detail['actual_quantity'] - $detail['system_quantity'];
                $detail['difference_type'] = $detail['difference_quantity'] > 0 ? 'surplus' : ($detail['difference_quantity'] < 0 ? 'shortage' : null);

                InventoryAuditDetail::create([
                    'inventory_audit_id' => $audit->id,
                    'product_id' => $detail['product_id'],
                    'units_id' => $detail['units_id'],
                    'system_quantity' => $detail['system_quantity'],
                    'actual_quantity' => $detail['actual_quantity'],
                    'difference_quantity' => $detail['difference_quantity'],
                    'difference_type' => $detail['difference_type'],
                    'reason' => $detail['reason'],
                    'notes' => $detail['notes'],
                ]);
            }

            DB::commit();
            session()->flash('success', 'تم حفظ الجرد بنجاح');
            return redirect()->route('inventory.audit.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء حفظ الجرد: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.Program.create-inventory-audit', [
            'stores' => storemaindata::select('id', 'name_ar')->get(),
        ]);
    }
} 