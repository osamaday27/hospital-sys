<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\supply_chain\StoreMovement;
use App\Models\supply_chain\storemaindata;
use App\Models\suppliers\suppliersdata;
use Illuminate\Support\Facades\DB;

class ViewDirectMovements extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $store_id = '';
    public $supplier_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function rollback($movementNumber)
    {
        $this->movement_items = StoreMovement::where('movement_number', $movementNumber)->get();

        DB::transaction(function () {
            foreach ($this->movement_items as $item) {
                productstorebalance::where('product_id', $item->product_id)
                    ->where('store_id', $item->store_id)
                    ->where('units_id', $item->units_id)
                    ->where('batch_no', $item->batch_no)
                    ->where('expiry_date', $item->expiry_date)
                    ->decrement('quantity', $item->quantity);

                $item->delete();
            }
        });

        session()->flash('success', 'تم الرجوع في الحركة بنجاح.');
        return redirect()->route('storemovement');
    }

    public function render()
    {
        $movements = StoreMovement::select(
            DB::raw('MIN(id) as id'),
            'movement_number',
            DB::raw('MAX(store_id) as store_id'),
            DB::raw('MAX(supplier_id) as supplier_id'),
            DB::raw('MAX(movement_date) as movement_date'),
            DB::raw('MAX(user_id) as user_id'),
            DB::raw('MAX(created_at) as created_at')
        )
        ->where('movement_type', 'direct_addition')
        ->when($this->store_id, fn($q) => $q->where('store_id', $this->store_id))
        ->when($this->supplier_id, fn($q) => $q->where('supplier_id', $this->supplier_id))
        ->when($this->search, function ($q) {
            $q->whereIn('movement_number', function ($sub) {
                $sub->select('movement_number')
                    ->from('store_movements')
                    ->whereHas('product', fn($p) =>
                        $p->where('name_ar', 'like', "%{$this->search}%")
                          ->orWhere('product_code', 'like', "%{$this->search}%")
                    );
            });
        })
        ->groupBy('movement_number')
        ->orderByDesc('movement_date')
        ->paginate(10);


        // نضيف عدد الأصناف الفريدة داخل كل حركة
        foreach ($movements as $movement) {
            $movement->items_count = StoreMovement::where('movement_number', $movement->movement_number)
                ->distinct('product_id')
                ->count('product_id');
        }

        return view('livewire.supply-chain.program.view-direct-movements', [
            'movements' => $movements,
            'stores' => storemaindata::all(),
            'suppliers' => suppliersdata::all(),
        ]);
    }
}