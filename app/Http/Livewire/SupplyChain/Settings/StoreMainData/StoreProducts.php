<?php

namespace App\Http\Livewire\SupplyChain\Settings\StoreMainData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\supply_chain\storeproductmodel;
use App\models\supply_chain\productdata;       // كما في مشروعك (m صغيرة)
use App\Models\supply_chain\ProductUnit;        // جدول product_units مع علاقات largeUnit/smallUnit
use App\Models\supply_chain\productcategory;    // جدول المجموعات
use App\Traits\UnitConversionHelpers;

class StoreProducts extends Component
{
    use WithPagination, UnitConversionHelpers;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['storeIdUpdated' => 'updateStoreId'];

    // مدخلات
    public $storeId;
    public $product_id;
    public $category_id;     // ✅ جديد: اختيار مجموعة
    public $status = 'active';
    public $delete_id = null;

    // بحث + ترقيم
    public $perPage = 25;
    public $search = '';     // ✅ جديد: مربع البحث

    // قيم صفوف الجدول (inline)
    public $rows = [];

    // خريطة وحدات كل منتج (بقيمة product_units.id)
    public $unitsByProduct = [];

    public function mount($storeId = null)
    {
        $this->storeId = $storeId;
    }

    public function updatingStoreId()
    {
        $this->resetPage();
    }

    public function updateStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->resetPage();
    }

    // ✅ عند تغيير البحث نرجع لأول صفحة
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addProduct()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        $this->validate([
            'product_id' => 'required|exists:products_data,id',
        ]);

        $exists = storeproductmodel::where('store_id', $this->storeId)
            ->where('product_id', $this->product_id)
            ->exists();

        if ($exists) {
            $this->addError('product_id', __('supply_chain_trans.product_already_added'));
            return;
        }

        storeproductmodel::create([
            'store_id'   => $this->storeId,
            'product_id' => $this->product_id,
            'status'     => $this->status,
        ]);

        $this->reset('product_id', 'status');
        $this->resetPage();

        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.product_added_successfully'),
            'type'    => 'success',
            'classes' => 'bg-success text-white'
        ]);
    }

    // ✅ جديد: إضافة جميع أصناف مجموعة دفعة واحدة مع تجنب التكرار
    public function addCategoryProducts()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        $this->validate([
            'category_id' => 'required|exists:product_categories,id',
        ]);

        // اجلب كل أصناف هذه المجموعة (يمكن تقييدها بالحالة لو رغبت: ->where('status','active'))
        $productIds = productdata::where('product_category_id', $this->category_id)
            ->pluck('id');

        if ($productIds->isEmpty()) {
            $this->dispatchBrowserEvent('toast', [
                'message' => __('supply_chain_trans.no_products_in_category') ?? 'لا توجد أصناف في هذه المجموعة.',
                'type'    => 'info',
                'classes' => 'bg-info text-white'
            ]);
            return;
        }

        // الأصناف الموجودة سلفًا في المخزن
        $existingInStore = storeproductmodel::where('store_id', $this->storeId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        // الأصناف الجديدة فقط
        $newIds = array_values(array_diff($productIds->toArray(), $existingInStore));

        if (empty($newIds)) {
            $this->dispatchBrowserEvent('toast', [
                'message' => __('supply_chain_trans.all_category_products_already_added') ?? 'كل أصناف المجموعة مضافة مسبقًا.',
                'type'    => 'info',
                'classes' => 'bg-info text-white'
            ]);
            return;
        }

        $now  = now();
        $rows = array_map(function ($pid) use ($now) {
            return [
                'store_id'   => $this->storeId,
                'product_id' => $pid,
                'status'     => $this->status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $newIds);

        // إدراج دفعى
        storeproductmodel::insert($rows);

        $added   = count($newIds);
        $total   = $productIds->count();
        $skipped = $total - $added;

        // نظف الإختيار + ارجع لأول صفحة
        $this->reset('category_id');
        $this->resetPage();

        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.bulk_added_summary', ['added' => $added, 'skipped' => $skipped])
                ?? "تمت إضافة {$added} صنف(ًا) من المجموعة. تم تجاهل {$skipped} لوجودها مسبقًا.",
            'type'    => 'success',
            'classes' => 'bg-success text-white'
        ]);
    }

    public function confirmDelete($id)
    {
        $this->delete_id = $id;
        $this->dispatchBrowserEvent('show-delete-modal1');
    }

    public function delete()
    {
        if (!$this->storeId) {
            $this->addError('storeId', __('supply_chain_trans.no_store_selected'));
            return;
        }

        $product = storeproductmodel::where('id', $this->delete_id)
            ->where('store_id', $this->storeId)
            ->first();

        if ($product) {
            $product->delete();

            $this->dispatchBrowserEvent('toast', [
                'message' => __('supply_chain_trans.product_deleted_successfully'),
                'type'    => 'danger',
                'classes' => 'bg-danger text-white'
            ]);
        }

        $this->delete_id = null;
        $this->dispatchBrowserEvent('hide-delete-modal');
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $record = storeproductmodel::findOrFail($id);
        $record->status = $record->status === 'active' ? 'inactive' : 'active';
        $record->save();

        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.status_changed_successfully'),
            'type'    => 'info',
            'classes' => 'bg-info text-white'
        ]);
    }

    /**
     * حفظ فوري لحقل واحد عند الخروج/التغيير (مع تحققات منطقية)
     */
    public function saveInline($rowId, $field)
    {
        $key   = "rows.$rowId.$field";
        $value = data_get($this->rows, "$rowId.$field");

        $record = storeproductmodel::where('id', $rowId)
            ->where('store_id', $this->storeId)
            ->first();

        if (!$record) {
            $this->addError($key, __('supply_chain_trans.record_not_found'));
            return;
        }

        $isQty  = in_array($field, ['order_min_qty','order_max_qty','reorder_point_qty'], true);
        $isUnit = in_array($field, ['order_min_unit_id','order_max_unit_id','reorder_point_unit_id'], true);

        // --- كميات ---
        if ($isQty) {
            if ($value === '' || $value === null) {
                $record->update([$field => null]);
                $this->rows[$rowId][$field] = null;
                $this->resetErrorBag("rows.$rowId.$field");
                if (in_array($field, ['order_min_qty','order_max_qty'])) {
                    $this->resetErrorBag("rows.$rowId.order_min_qty");
                    $this->resetErrorBag("rows.$rowId.order_max_qty");
                }
                $this->toastOk();
                return;
            }

            if (!is_numeric($value)) {
                $this->rejectWithToast($rowId, $field, $record, __('supply_chain_trans.must_be_number'));
                return;
            }

            $num = (float)$value;
            if ($num < 0) {
                $this->rejectWithToast($rowId, $field, $record, __('supply_chain_trans.no_negative_values'));
                return;
            }

            if (in_array($field, ['order_min_qty','order_max_qty'])) {
                $minQty  = ($field === 'order_min_qty')  ? $num : (data_get($this->rows, "$rowId.order_min_qty") ?? null);
                $maxQty  = ($field === 'order_max_qty')  ? $num : (data_get($this->rows, "$rowId.order_max_qty") ?? null);
                $minPUId = data_get($this->rows, "$rowId.order_min_unit_id");
                $maxPUId = data_get($this->rows, "$rowId.order_max_unit_id");

                if ($minQty !== null && $maxQty !== null && $minPUId && $maxPUId) {
                    $ok = $this->compareMinMaxInBase((int)$record->product_id, (float)$minQty, (int)$minPUId, (float)$maxQty, (int)$maxPUId);
                    if ($ok === false) {
                        $this->addError("rows.$rowId.order_max_qty", __('supply_chain_trans.max_must_be_greater_than_min'));
                        $this->dispatchBrowserEvent('toast', [
                            'message' => __('supply_chain_trans.max_must_be_greater_than_min'),
                            'type'    => 'error',
                            'classes' => 'bg-danger text-white'
                        ]);
                        $this->rows[$rowId][$field] = $record->$field;
                        return;
                    }
                }
            }

            $record->update([$field => $num]);
            $this->rows[$rowId][$field] = $num;
            $this->resetErrorBag("rows.$rowId.$field");
            if (in_array($field, ['order_min_qty','order_max_qty'])) {
                $this->resetErrorBag("rows.$rowId.order_min_qty");
                $this->resetErrorBag("rows.$rowId.order_max_qty");
            }
            $this->toastOk();
            return;
        }

        // --- وحدات (نخزن product_units.id) ---
        if ($isUnit) {
            if ($value === '' || $value === null) {
                $record->update([$field => null]);
                $this->rows[$rowId][$field] = null;
                $this->resetErrorBag("rows.$rowId.$field");
                $this->toastOk();
                return;
            }

            $productUnitId = (int)$value;

            $valid = ProductUnit::where('id', $productUnitId)
                ->where('product_id', $record->product_id)
                ->exists();

            if (!$valid) {
                $this->rejectWithToast($rowId, $field, $record, __('supply_chain_trans.unit_not_for_this_product'));
                return;
            }

            if (in_array($field, ['order_min_unit_id','order_max_unit_id'])) {
                $minQty  = data_get($this->rows, "$rowId.order_min_qty");
                $maxQty  = data_get($this->rows, "$rowId.order_max_qty");

                $minPUId = ($field === 'order_min_unit_id') ? $productUnitId : data_get($this->rows, "$rowId.order_min_unit_id");
                $maxPUId = ($field === 'order_max_unit_id') ? $productUnitId : data_get($this->rows, "$rowId.order_max_unit_id");

                if ($minQty !== null && $maxQty !== null && $minPUId && $maxPUId) {
                    $ok = $this->compareMinMaxInBase((int)$record->product_id, (float)$minQty, (int)$minPUId, (float)$maxQty, (int)$maxPUId);
                    if ($ok === false) {
                        $this->addError("rows.$rowId.order_max_qty", __('supply_chain_trans.max_must_be_greater_than_min'));
                        $this->dispatchBrowserEvent('toast', [
                            'message' => __('supply_chain_trans.max_must_be_greater_than_min'),
                            'type'    => 'error',
                            'classes' => 'bg-danger text-white'
                        ]);
                        $this->rows[$rowId][$field] = $record->$field;
                        return;
                    }
                }
            }

            $record->update([$field => $productUnitId]);
            $this->rows[$rowId][$field] = $productUnitId;
            $this->resetErrorBag("rows.$rowId.$field");
            $this->resetErrorBag("rows.$rowId.order_min_qty");
            $this->resetErrorBag("rows.$rowId.order_max_qty");
            $this->toastOk();
            return;
        }

        $this->addError($key, __('supply_chain_trans.unsupported_field'));
    }

    /**
     * يقارن (min, max) بعد تحويلهما إلى أصغر وحدة.
     * true لو max>min، false لو العكس، null لو تعذر المقارنة.
     */
    protected function compareMinMaxInBase(int $productId, float $minQty, int $minPUId, float $maxQty, int $maxPUId): ?bool
    {
        $minUnitId = $this->resolveUnitsIdFromProductUnit($productId, $minPUId);
        $maxUnitId = $this->resolveUnitsIdFromProductUnit($productId, $maxPUId);
        if (!$minUnitId || !$maxUnitId) return null;

        try {
            $minBase = $this->ucToBase($productId, $minQty, $minUnitId);
            $maxBase = $this->ucToBase($productId, $maxQty, $maxUnitId);
            return $maxBase > $minBase;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * تحويل product_units.id إلى units.id الفعلية:
     * - أول سجل للمنتج => الكبيرة
     * - غير ذلك => الصغيرة إن وُجدت، وإلا الكبيرة
     */
    protected function resolveUnitsIdFromProductUnit(int $productId, int $productUnitId): ?int
    {
        $pu = ProductUnit::select('id','product_id','unit_large_id','unit_small_id')
            ->where('id', $productUnitId)
            ->where('product_id', $productId)
            ->first();

        if (!$pu) return null;

        $firstId = ProductUnit::where('product_id', $productId)
            ->orderBy('product_id')
            ->orderBy('id')
            ->value('id');

        $isFirst = ($pu->id == $firstId);

        if ($isFirst) {
            return (int) $pu->unit_large_id;
        }

        return $pu->unit_small_id ? (int)$pu->unit_small_id : (int)$pu->unit_large_id;
    }

    protected function rejectWithToast($rowId, $field, $record, $message)
    {
        $this->addError("rows.$rowId.$field", $message);
        $this->rows[$rowId][$field] = $record->$field;
        $this->dispatchBrowserEvent('toast', [
            'message' => $message,
            'type'    => 'error',
            'classes' => 'bg-danger text-white'
        ]);
    }

    protected function toastOk()
    {
        $this->dispatchBrowserEvent('toast', [
            'message' => __('supply_chain_trans.saved_successfully'),
            'type'    => 'success',
            'classes' => 'bg-success text-white'
        ]);
    }

    public function render()
    {
        // قوائم الاختيار
        $products    = productdata::select('id', 'name_ar', 'name_en')->orderBy('name_ar')->get();
        $categories  = productcategory::select('id','name_ar','name_en')->orderBy('name_ar')->get();

        // بيانات الجدول مع بحث
        $search = trim($this->search ?? '');

        $storeProducts = storeproductmodel::with([
                'product:id,product_code,barcode,unified_code,name_ar,name_en,product_category_id',
                'product.category:id,name_ar,name_en',
            ])
            ->where('store_id', $this->storeId)
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name_ar', 'like', "%{$search}%")
                       ->orWhere('name_en', 'like', "%{$search}%")
                       ->orWhere('product_code', 'like', "%{$search}%")
                       ->orWhere('barcode', 'like', "%{$search}%")
                       ->orWhere('unified_code', 'like', "%{$search}%")
                       ->orWhereHas('category', function ($cq) use ($search) {
                           $cq->where('name_ar', 'like', "%{$search}%")
                              ->orWhere('name_en', 'like', "%{$search}%");
                       });
                });
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);

        // بناء خيارات الوحدات لكل منتج بالقيمة product_units.id
        $productIds = $storeProducts->pluck('product_id')->filter()->unique()->values();

        if ($productIds->isNotEmpty()) {
            $pus = ProductUnit::with(['largeUnit:id,name_ar,name_en', 'smallUnit:id,name_ar,name_en'])
                ->whereIn('product_id', $productIds)
                ->orderBy('product_id')
                ->orderBy('id')
                ->get(['id','product_id','unit_large_id','unit_small_id']);

            $byProd   = [];
            $seenFirst = [];

            foreach ($pus as $pu) {
                $pid = $pu->product_id;

                $largeNameAr = $pu->largeUnit?->name_ar ?? ('#'.$pu->unit_large_id);
                $largeNameEn = $pu->largeUnit?->name_en ?? ('#'.$pu->unit_large_id);
                $smallNameAr = $pu->smallUnit?->name_ar ?? null;
                $smallNameEn = $pu->smallUnit?->name_en ?? null;

                $isFirst = !isset($seenFirst[$pid]);
                $seenFirst[$pid] = true;

                if ($isFirst) {
                    $labelAr = $largeNameAr;
                    $labelEn = $largeNameEn;
                } else {
                    if ($smallNameAr && $smallNameEn) {
                        $labelAr = $smallNameAr;
                        $labelEn = $smallNameEn;
                    } else {
                        $labelAr = $largeNameAr;
                        $labelEn = $largeNameEn;
                    }
                }

                $byProd[$pid][] = [
                    'id'      => $pu->id,   // product_units.id
                    'name_ar' => $labelAr,
                    'name_en' => $labelEn,
                ];
            }

            $this->unitsByProduct = $byProd;
        } else {
            $this->unitsByProduct = [];
        }

        // تهيئة rows لأول مرة لكل صف
        foreach ($storeProducts as $row) {
            if (!isset($this->rows[$row->id])) {
                $this->rows[$row->id] = [
                    'order_min_qty'         => $row->order_min_qty,
                    'order_min_unit_id'     => $row->order_min_unit_id,     // product_units.id
                    'order_max_qty'         => $row->order_max_qty,
                    'order_max_unit_id'     => $row->order_max_unit_id,     // product_units.id
                    'reorder_point_qty'     => $row->reorder_point_qty,
                    'reorder_point_unit_id' => $row->reorder_point_unit_id, // product_units.id
                ];
            }
        }

        return view('livewire.supply-chain.settings.store-main-data.store-products', [
            'products'       => $products,
            'categories'     => $categories,     // ✅ لعرض قائمة المجموعات في الفيو
            'storeProducts'  => $storeProducts,
            'unitsByProduct' => $this->unitsByProduct,
        ]);
    }
}
