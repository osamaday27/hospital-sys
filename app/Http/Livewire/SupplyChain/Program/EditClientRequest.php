<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\{
    ClientItemRequest,
    ClientItemRequestItem,
    storemaindata,
    productdata,
    productunit
};
use App\Models\administrative\medical_record;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Str;

class EditClientRequest extends Component
{
    public $request_id;
    public $client_id, $store_id, $request_date, $reason;
    public $items = [];

    public $clients = [], $client_search = '', $client_page = 1;
    public $stores = [], $store_search = '', $store_page = 1;
    public $products = [], $product_search = '', $product_page = 1;
    public $per_page = 10;

    public function mount($id)
    {
        $this->request_id = $id;
        $this->loadRequest();
        $this->loadClients();
        $this->loadStores();
        $this->loadProducts();
    }

    protected function loadRequest()
    {
        $request = ClientItemRequest::with('items')->findOrFail($this->request_id);
        $this->client_id = $request->client_id;
        $this->store_id = $request->store_id;
        $this->request_date = $request->request_date;
        $this->reason = $request->reason;

        $this->items = $request->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_id' => $item->unit_id,
                'related_units' => $this->getProductUnits($item->product_id),
            ];
        })->toArray();
    }

    public function updatedClientSearch()  { $this->client_page = 1; $this->loadClients(); }
    public function updatedStoreSearch()   { $this->store_page = 1;  $this->loadStores(); }
    public function updatedProductSearch() { $this->product_page = 1; $this->loadProducts(); }

    public function loadClients()
    {
        $this->clients = medical_record::query()
            ->when($this->client_search, fn($q) => $q->where('full_name', 'like', "%{$this->client_search}%"))
            ->select('id', 'full_name', 'medical_number')
            ->orderBy('full_name')
            ->paginate($this->per_page, ['*'], 'page', $this->client_page)
            ->items();
    }

    public function loadStores()
    {
        $this->stores = storemaindata::query()
            ->when($this->store_search, fn($q) => $q->where('name_ar', 'like', "%{$this->store_search}%"))
            ->select('id', 'name_ar', 'manual_code')
            ->orderBy('name_ar')
            ->paginate($this->per_page, ['*'], 'page', $this->store_page)
            ->items();
    }

    public function loadProducts()
    {
        $this->products = productdata::query()
            ->when($this->product_search, fn($q) => $q->where('name_ar', 'like', "%{$this->product_search}%"))
            ->select('id', 'name_ar', 'product_code')
            ->orderBy('name_ar')
            ->paginate($this->per_page, ['*'], 'page', $this->product_page)
            ->items();
    }

    public function updated($property)
    {
        if (Str::startsWith($property, 'items.') && Str::endsWith($property, '.product_id')) {
            $index = explode('.', $property)[1];
            $this->updateProductUnits($index);
        }
    }

    protected function updateProductUnits($index)
    {
        $productId = $this->items[$index]['product_id'];
        $this->items[$index]['related_units'] = $this->getProductUnits($productId);
        $this->items[$index]['unit_id'] = null;
    }

    protected function getProductUnits($productId)
    {
        return productunit::with(['largeUnit', 'smallUnit'])
            ->where('product_id', $productId)
            ->get()
            ->flatMap(function ($unit) {
                return [
                    [
                        'id' => $unit->unit_large_id,
                        'name_ar' => optional($unit->largeUnit)->name_ar,
                        'name_en' => optional($unit->largeUnit)->name_en,
                    ],
                    [
                        'id' => $unit->unit_small_id,
                        'name_ar' => optional($unit->smallUnit)->name_ar,
                        'name_en' => optional($unit->smallUnit)->name_en,
                    ],
                ];
            })->filter(fn($u) => !is_null($u['id']))
              ->unique('id')->values()->toArray();
    }

    public function save()
    {
        $this->validate([
            'client_id' => 'required|exists:patient,id',
            'store_id' => 'required|exists:store_main_date,id',
            'request_date' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products_data,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
        ]);

        DB::transaction(function () {
            $request = ClientItemRequest::findOrFail($this->request_id);
            $request->update([
                'client_id' => $this->client_id,
                'store_id' => $this->store_id,
                'request_date' => $this->request_date,
                'reason' => $this->reason,
                'updated_by' => Auth::id(),
            ]);

            $request->items()->delete();
            foreach ($this->items as $item) {
                $request->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'created_by' => Auth::id(),
                ]);
            }

            session()->flash('success', __('supply_chain_trans.update_success'));
        });
    }
    public function addItem()
    {
        $this->items[] = $this->emptyItem();
    }
    
    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }
    
    protected function emptyItem()
    {
        return [
            'product_id' => null,
            'quantity' => 1,
            'unit_id' => null,
            'related_units' => [],
        ];
    }
    
    public function render()
    {
        return view('livewire.supply-chain.program.edit-client-request');
    }
}