<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\storeItemRequest;
use App\Models\supply_chain\storeItemRequestDetail;
use App\Models\supply_chain\StoreMainData;
use App\Models\supply_chain\ProductData;
use App\Models\supply_chain\Unit;
use App\Models\supply_chain\ProductUnit;
use App\Models\supply_chain\ProductStoreBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class storeRequestCreate extends Component
{
    public $store_to_id, $store_id, $request_date, $reason;
    public $items = [];
    public $available_products;

    // حقل البحث عن الأصناف
    public $productSearch = '';

    protected $rules = [
        'store_id'                       => 'required|exists:store_main_date,id',
        'store_to_id'                    => 'required|different:store_id|exists:store_main_date,id',
        'request_date'                   => 'required|date',
        'items.*.product_id'             => 'required|exists:products_data,id',
        'items.*.unit_id'                => 'required|exists:units,id',
        'items.*.quantity'               => 'required|numeric|min:0.01',
    ];

    protected $messages = [
        'store_to_id.different' => 'لا يمكن اختيار نفس المخزن كمصدر ووجهة.',
    ];

    public function mount()
    {
        $this->request_date = now()->format('Y-m-d');
        $this->items[] = $this->emptyItem();
        $this->available_products = collect();
    }

    public function emptyItem()
    {
        return [
            'product_id'      => '',
            'unit_id'         => '',
            'quantity'        => '',
            'related_units'   => [],
            'current_balance' => null,
            'cost_price'      => null,
            'sale_price'      => null,
            // حقول إضافية محفوظة كما هي لو حبيت تستخدمها لاحقاً
            'name'            => null,
            'code'            => null,
            'batch_no'        => null,
            'expiry_date'     => null,
        ];
    }

    public function updated($propertyName)
    {
        // عند تغيير المخزن المصدر: نفلتر المنتجات المتاحة فقط من هذا المخزن
        if ($propertyName === 'store_id') {
            $this->filterProductsByStore($this->productSearch);
            // تفريغ وحدات/أرصدة العناصر لأن المخزن المصدر تغيّر
            foreach ($this->items as $i => $it) {
                $this->items[$i]['unit_id']         = '';
                $this->items[$i]['related_units']   = [];
                $this->items[$i]['current_balance'] = null;
                $this->items[$i]['cost_price']      = null;
                $this->items[$i]['sale_price']      = null;
            }
        }

        // تحديث وحدات المنتج المختار لكل صف
        foreach ($this->items as $index => $item) {
            if ($propertyName === "items.$index.product_id") {
                $productId = $item['product_id'];
                $this->items[$index]['unit_id'] = '';
                $this->items[$index]['related_units'] = $this->getUnitsForProduct($productId);

                // نسخ batch/expiry من الصف السابق (لو موجود)
                if ($index > 0) {
                    $this->items[$index]['batch_no']    = $this->items[$index - 1]['batch_no'] ?? '';
                    $this->items[$index]['expiry_date'] = $this->items[$index - 1]['expiry_date'] ?? '';
                }
            }
        }
    }

    // عند تغيير قيمة حقل البحث
    public function updatedProductSearch()
    {
        $this->filterProductsByStore($this->productSearch);
    }

    public function updatedItems($value, $name)
    {
        // يدعم Livewire: $name = "index.field"
        [$index, $field] = explode('.', $name);

        if ($field === 'product_id' && $value) {
            $product = ProductData::find($value);
            $this->items[$index]['name'] = $product?->name_ar;
            $this->items[$index]['code'] = $product?->product_code;

            // تحميل الوحدات للمنتج
            $this->items[$index]['related_units'] = $this->getUnitsForProduct($value);
        }

        if ($field === 'unit_id' && !empty($this->items[$index]['product_id']) && !empty($this->store_id)) {
            $unitId    = $value;
            $productId = $this->items[$index]['product_id'];

            // الرصيد من المخزن "المصدر" فقط
            $balance = ProductStoreBalance::where('product_id', $productId)
                ->where('store_id', $this->store_id)
                ->where('units_id', $unitId)
                ->sum('quantity');

            // معلومات الأسعار من ProductUnit
            $unitInfo = ProductUnit::where('product_id', $productId)
                ->where(function ($q) use ($unitId) {
                    $q->where('unit_large_id', $unitId)->orWhere('unit_small_id', $unitId);
                })
                ->first();

            $this->items[$index]['current_balance'] = $balance ?? 0;
            $this->items[$index]['cost_price']      = $unitInfo->cost_price ?? 0;
            $this->items[$index]['sale_price']      = $unitInfo->sale_price ?? 0;
        }
    }

    /**
     * جلب المنتجات المرتبطة بالمخزن المصدر + بحث بالاسم/الكود.
     * لو عايز تقصر النتائج (limit) تقدر تضيف ->take(50).
     */
    public function filterProductsByStore($term = null)
    {
        if (!$this->store_id) {
            $this->available_products = collect();
            return;
        }

        $query = ProductData::query()
            ->whereHas('productstorebalance', function ($q) {
                $q->where('store_id', $this->store_id);
            });

        // البحث بالاسم العربي/الإنجليزي أو كود المنتج
        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('name_ar', 'like', '%'.$term.'%')
                  ->orWhere('name_en', 'like', '%'.$term.'%')
                  ->orWhere('product_code', 'like', '%'.$term.'%');
            });
        }

        // إزالة التكرار لو ظهر نفس المنتج أكثر من مرة بسبب تعدد أرصدة الوحدات
        $this->available_products = $query->select('id', 'name_ar', 'name_en', 'product_code')
            ->distinct()
            ->orderBy('name_ar')
            ->get();
    }

    /**
     * جلب الوحدات (كبير/صغير) لمنتج ما، بدون تغيير تصميمك الأصلي.
     */
    public function getUnitsForProduct($productId)
    {
        if (!$productId) return [];

        $units = ProductUnit::where('product_id', $productId)->get();
        $related = [];

        foreach ($units as $unit) {
            if ($unit->unit_large_id) {
                $u = Unit::find($unit->unit_large_id);
                if ($u) $related[] = $u;
            }
            if ($unit->unit_small_id) {
                $u = Unit::find($unit->unit_small_id);
                if ($u) $related[] = $u;
            }
        }

        // إزالة التكرار والحفاظ على نفس البنية المستخدمة في الواجهة
        return collect($related)->unique('id')->values()->all();
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
        // حماية إضافية قبل الـ validate
        if ($this->store_id && $this->store_to_id && $this->store_id == $this->store_to_id) {
            $this->addError('store_to_id', 'لا يمكن التحويل لنفس المخزن.');
            return;
        }

        $this->validate();

        DB::transaction(function () {
            // إنشاء رأس الطلب (تحويل من مخزن إلى مخزن)
            $request = storeItemRequest::create([
                'store_to_id'     => $this->store_to_id, // المخزن الوجهة
                'store_id'        => $this->store_id,    // المخزن المصدر
                'request_date'    => $this->request_date,
                'reason'          => $this->reason,
                'status'          => 'new',
                'request_number'  => 'REQ-' . now()->format('YmdHis'),
                'created_by'      => Auth::id(),
                // ممكن تضيف type => 'store_to_store' لو عندك عمود للتمييز
            ]);

            // تفاصيل الأصناف
            foreach ($this->items as $item) {
                storeItemRequestDetail::create([
                    'request_id'  => $request->id,
                    'product_id'  => $item['product_id'],
                    'unit_id'     => $item['unit_id'],
                    'quantity'    => $item['quantity'],
                    'created_by'  => Auth::id(),
                    // احتفظت بباقي الحقول لو احتجتها لاحقاً (batch_no, expiry_date ...)
                ]);
            }
        });

        session()->flash('success', 'تم حفظ الطلب بنجاح.');
        $this->reset(['store_to_id', 'store_id', 'reason', 'items', 'productSearch']);
        $this->request_date = now()->format('Y-m-d');
        $this->items[] = $this->emptyItem();
        $this->available_products = collect();
    }

    public function render()
    {
        return view('livewire.supply-chain.program.store-request-create', [
            'stores'   => StoreMainData::all(),
            'products' => $this->available_products,
            'units'    => Unit::all(),
        ]);
    }
}