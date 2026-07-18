<?php


namespace App\Http\Livewire\SupplyChain\Settings\ProductsData;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\supply_chain\productdata;
use App\Models\supply_chain\ProductCategory;
use App\Models\supply_chain\supplychaingeneralsetting;
use App\Traits\web\file_storage;
use Illuminate\Support\Facades\Storage;

class BasicData extends Component
{
    use WithFileUploads, file_storage;

    // حقول البيانات
    public $name_ar;
    public $name_en;
    public $system_code;
    public $barcode;
    public $product_code;
    public $profit_margin;
    public $product_category_id;
    public $status;
    public $description;
    public $unified_code;
    public $product_type;
    public $is_critical=false;
    public $ledger_number;
    public $vat = false;
    public $vat_value;
    public $expiration_date;
    public $images = [];

    // للمفاضلة بين إنشاء/تعديل
    public $productId;
    public $existingImages = [];

    // إعدادات التكويد
    public $item_coding_method;   // 'manual', 'system_serial', 'groups'
    public $item_code_length;     // طول الجزء الرقمي في طريقة 'groups'

    public function mount($productId = null)
    {
        $this->productId = $productId;

        // 1. جلب إعدادات التكويد من الجدول
        $setting = supplychaingeneralsetting::first();
        $this->item_coding_method = $setting->item_coding_method;
        $this->item_code_length = $setting->item_code_length;

        // 2. إذا لم تكن الطريقة يدوية، مولّد الكود فوراً
        if ($this->item_coding_method !== 'manual') {
            $this->generateSystemCode();
        }

        // 3. إذا كان لدينا ID للتعديل، نعبئ الحقول من النموذج
        if ($productId) {
            $product = productdata::findOrFail($productId);

            $this->name_ar             = $product->name_ar;
            $this->name_en             = $product->name_en;
            $this->system_code         = $product->system_code;
            $this->barcode             = $product->barcode;
            $this->product_code        = $product->product_code;
            $this->profit_margin       = $product->profit_margin;
            $this->product_category_id = $product->product_category_id;
            $this->status              = $product->status;
            $this->description         = $product->description;
            $this->unified_code        = $product->unified_code;
            $this->product_type        = $product->product_type;
            $this->is_critical         = (bool) $product->is_critical;
            $this->ledger_number       = $product->ledger_number;
            $this->vat                 = $product->vat;
            $this->vat_value           = $product->vat_value;
            $this->expiration_date     = $product->expiration_date;
            $this->existingImages      = json_decode($product->images, true) ?? [];
        }
    }

    /**
     * عند تغيير المجموعة، نعيد توليد الكود إذا كانت الطريقة 'groups'
     */
    public function updatedProductCategoryId($value)
    {
        if ($this->item_coding_method === 'groups') {
            $this->generateSystemCode();
        }
    }

    /**
     * قواعد التحقق الديناميكية (مع استثناء الـ ID نفسه من فحص الـ unique)
     */
    protected function rules()
    {
        return [
            'name_ar'             => 'required|string',
            'name_en'             => 'required|string',
            'system_code'         => 'required|unique:products_data,system_code,' . ($this->productId ?? 'NULL') . ',id',
            'product_category_id' => 'required|exists:product_categories,id',
            'status'              => 'required|in:active,inactive',
            'description'         => 'nullable|string',
            'barcode'             => 'nullable|string',
            'product_code'        => 'nullable|string',
            'unified_code'        => 'nullable|string',
            'profit_margin'       => 'nullable|numeric',
            'product_type'        => 'nullable|in:local,imported',
            'is_critical'         => 'boolean',
            'ledger_number'       => 'nullable|string',
            'vat'                 => 'boolean',
            'vat_value'           => 'required_if:vat,true|nullable|numeric',
            'expiration_date'     => 'nullable|date',
            'images.*'            => 'nullable|image|max:5120',
        ];
    }

    /**
     * حفظ أو تحديث البيانات
     */
    public function saveData()
    {
        $this->validate();

        $data = [
            'name_ar'             => $this->name_ar,
            'name_en'             => $this->name_en,
            'system_code'         => $this->system_code,
            'barcode'             => $this->barcode,
            'product_code'        => $this->product_code,
            'profit_margin'       => $this->profit_margin,
            'product_category_id' => $this->product_category_id,
            'status'              => $this->status,
            'description'         => $this->description,
            'unified_code'        => $this->unified_code,
            'product_type'        => $this->product_type,
            'is_critical'         => (bool) $this->is_critical,
            'ledger_number'       => $this->ledger_number,
            'vat'                 => $this->vat,
            'vat_value'           => $this->vat ? $this->vat_value : null,
            'expiration_date'     => $this->expiration_date,
        ];

        if ($this->productId) {
            $product = productdata::findOrFail($this->productId);
            $product->update($data);
        } else {
            $product = productdata::create($data);
            $this->productId = $product->id;
        }

        // رفع الصور الجديدة
        if (!empty($this->images)) {
            try {
                $storedPaths = $this->file_storage($this->images, 'products');
                $this->existingImages = array_merge($this->existingImages, $storedPaths);
                $product->update([
                    'images' => json_encode($this->existingImages, JSON_UNESCAPED_SLASHES),
                ]);
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('show-toast', [
                    'message' => 'فشل رفع الملف: ' . $e->getMessage(),
                    'type'    => 'error',
                ]);
                return;
            }
        }

        $this->dispatchBrowserEvent('show-toast', [
            'message' => trans('supply_chain_trans.product_saved_successfully'),
            'type'    => 'success',
        ]);

        $this->emit('productSaved', $product->id);
        $this->emit('goToTab', 'tab2');
    }

    /**
     * يختار دالة التوليد المناسبة بناءً على الإعداد
     */
    protected function generateSystemCode()
    {
        if ($this->item_coding_method === 'system_serial') {
            $this->system_code = $this->generateSystemSerial();
        } elseif ($this->item_coding_method === 'groups') {
            $this->system_code = $this->generateByGroup();
        }
    }

    /**
     * طريقة system_serial: نأخذ أكبر id موجود ونضيف 1
     */
    protected function generateSystemSerial()
    {
        $lastId = productdata::max('id');
        return $lastId ? $lastId + 1 : 1;
    }

    /**
     * طريقة groups: {group_id}-{رقم_الصنف_مُنسّق}
     */
    protected function generateByGroup()
    {
        $groupId = $this->product_category_id;
        $count   = productdata::where('product_category_id', $groupId)->count();
        $next    = $count + 1;
        return $groupId . '-' . str_pad($next, $this->item_code_length, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $categories = ProductCategory::all();
        return view('livewire.supply-chain.settings.products-data.basic-data', compact('categories'));
    }
}
