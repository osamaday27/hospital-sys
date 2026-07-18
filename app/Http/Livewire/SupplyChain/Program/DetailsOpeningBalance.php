<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\models\supply_chain\storemovement;
use App\models\supply_chain\productstorebalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DetailsOpeningBalance extends Component
{
    public $movement_number;
    public $movement_items = [];
    public $movement_summary;

    public function mount($movement_number)
    {
        $this->movement_number = $movement_number;

        $this->movement_items = storemovement::with(['product', 'unit', 'store', 'supplier', 'user'])
            ->where('movement_number', $movement_number)
            ->get();

        $this->movement_summary = $this->movement_items->first();
    }

    public function rollbackMovement()
    {
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
        return Redirect::route('storemovement');
    }

    public function render()
    {
        return view('livewire.supply-chain.program.details-opening-balance', [
            'movement_items' => $this->movement_items,
            'movement_summary' => $this->movement_summary,
        ]);
    }
}