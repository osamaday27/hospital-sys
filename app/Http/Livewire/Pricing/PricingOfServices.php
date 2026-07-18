<?php

namespace App\Http\Livewire\Pricing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\pricelistservice;
use App\Models\Finance\price_lists;
use App\Models\Finance\service;
use App\Models\general\branch;
use App\Models\Finance\servicesublevel;

class PricingOfServices extends Component
{
    use WithPagination;

    public $price_list_id;
    public $search = '';
    public $perPage = 10;
    public $branch_id = null;
    public $filter_priced = null;
    public $filter_status = null;
    public $filter_sublevel_id = null;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['updatePrice', 'storeOrUpdatePrice'];

    public function mount($price_list_id)
    {
        $this->price_list_id = $price_list_id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function updatingBranchId()
    {
        $this->resetPage();
    }
    public function updatingFilterPriced()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterSublevelId()
    {
        $this->resetPage();
    }

    public function updatePrice($id, $field, $value)
    {
        $item = pricelistservice::findOrFail($id);
        $item->$field = $value;
        $item->save();

        $this->dispatchBrowserEvent('show-success', ['message' => trans('pricing_list.price_updated_successfully')]);
    }

    public function getCounts()
    {
        $total = service::count();
        $priced = pricelistservice::where('price_list_id', $this->price_list_id)->whereNotNull('hospital_price')->count();
        $remaining = $total - $priced;

        return compact('total', 'priced', 'remaining');
    }

    public function storeOrUpdatePrice($service_id, $field, $value)
    {
        $item = pricelistservice::firstOrNew([
            'price_list_id' => $this->price_list_id,
            'service_id' => $service_id,
        ]);

        $item->$field = $value;
        $item->save();

        $this->dispatchBrowserEvent('show-success', [
            'message' => trans('pricing_list.price_updated_successfully'),
        ]);
    }

    public function render()
    {
        $price_list = price_lists::with('getcurrency')->findOrFail($this->price_list_id);

        $query = service::with([
            'pricelistservices' => function ($q) {
                $q->where('price_list_id', $this->price_list_id);
            },
            'sublevel',
        ]);

        // ✅ تحسين البحث (عربي / إنجليزي / كود / كود المستشفى) + تجاهل المسافات
        if (!empty(trim($this->search))) {
            $searchTerm = trim($this->search);

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name->ar', 'like', '%' . $searchTerm . '%')
                  ->orWhere('name->en', 'like', '%' . $searchTerm . '%')
                  ->orWhere('id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('hospital_code', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($this->filter_sublevel_id) {
            $query->where('sublevel_id', $this->filter_sublevel_id);
        }

        if ($this->filter_status !== null) {
            $query->where('status', $this->filter_status);
        }

        // ✅ فلترة الخدمات حسب التسعير
        if ($this->filter_priced === 'priced') {
            $query->whereHas('pricelistservices', function ($q) {
                $q->where('price_list_id', $this->price_list_id)->whereNotNull('hospital_price');
            });
        } elseif ($this->filter_priced === 'unpriced') {
            $query->where(function ($q) {
                $q->whereDoesntHave('pricelistservices', function ($q2) {
                    $q2->where('price_list_id', $this->price_list_id);
                })->orWhereHas('pricelistservices', function ($q3) {
                    $q3->where('price_list_id', $this->price_list_id)->whereNull('hospital_price');
                });
            });
        }

        $services = $query->paginate($this->perPage);
        $counts = $this->getCounts();
        $branches = branch::select('id', 'name')->get();
        $sublevels = servicesublevel::select('id', 'name')->get();

        return view('livewire.pricing.pricing-of-services', [
            'services' => $services,
            'price_list' => $price_list,
            'counts' => $counts,
            'branches' => $branches,
            'sublevels' => $sublevels,
        ]);
    }
}