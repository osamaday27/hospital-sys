<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\DamagedItemRequest;
use App\Models\supply_chain\DamagedItemRequestItem;
use App\Models\supply_chain\storemaindata;
use App\Models\supply_chain\productdata;
use App\Models\supply_chain\unit;
use App\Models\supply_chain\productunit;
use App\Models\supply_chain\productstorebalance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateDamagedRequest extends Component
{
    public $store_id, $request_date, $reason;
    public $items = [];

    public function mount()
    {
        $this->request_date = now()->format('Y-m-d');
        $this->items[] = $this->emptyItem();
    }

    public function emptyItem()
    {
        return [
            'product_id' => '',
            'unit_id' => '',
            'quantity' => '',
            'notes' => '',
            'related_units' => [],
            'current_balance' => 0
        ];
    }

    public function updated($propertyName)
    {
        foreach ($this->items as $index => $item) {
            if ($propertyName === "items.$index.product_id" && $item['product_id']) {
                $productId = $item['product_id'];

                // تحميل الوحدات المرتبطة بوحدة صغيرة
                $this->items[$index]['related_units'] = productunit::where('product_id', $productId)
                    ->with('unitSmall')
                    ->get()
                    ->map(function ($pu) {
                        return [
                            'id' => $pu->unitSmall->id ?? null,
                            'name_ar' => $pu->unitSmall->name_ar ?? '',
                            'name_en' => $pu->unitSmall->name_en ?? '',
                        ];
                    });

                // تحميل الرصيد من المخزن
                if ($this->store_id) {
                    $balance = productstorebalance::where('store_id', $this->store_id)
                        ->where('product_id', $productId)
                        ->first();

                    $this->items[$index]['current_balance'] = $balance->quantity ?? 0;
                }
            }
        }
    }

    public function addItem()
    {
        $this->items[] = $this->emptyItem();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate([
            'store_id' => 'required|exists:store_main_date,id',
            'request_date' => 'required|date',
            'items.*.product_id' => 'required|exists:products_data,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $request = DamagedItemRequest::create([
                'request_number' => 'DMG-' . strtoupper(Str::random(6)),
                'store_id' => $this->store_id,
                'request_date' => $this->request_date,
                'reason' => $this->reason,
                'user_id' => Auth::id(),
                'status' => 'new',
            ]);

            foreach ($this->items as $item) {
                DamagedItemRequestItem::create([
                    'request_id' => $request->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'],
                ]);
            }

            DB::commit();

            session()->flash('success', 'تم حفظ طلب التكهين بنجاح.');
            return redirect()->route('write_off_items');

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.create-damaged-request', [
            'stores' => storemaindata::select('id', 'name_ar')->get(),
            'products' => productdata::select('id', 'name_ar')->get(),
        ]);
    }
}