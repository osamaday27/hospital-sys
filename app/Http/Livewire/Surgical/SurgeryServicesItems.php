<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Finance\Service;
use App\Models\supply_chain\Productdata;
use App\Models\supply_chain\Productdata as Drug;
use App\Models\Medical\SurgeryServiceItem;
use App\Models\Medical\SurgeryItem;
use App\Models\Medical\SurgeryDrug;
use Illuminate\Support\Facades\Auth;

class SurgeryServicesItems extends Component
{
    public $surgery_id;
    public $services = [];
    public $items = [];
    public $drugs = [];

    public $service_id, $service_type, $service_notes;
    public $item_id, $item_quantity, $item_unit, $item_notes;
    public $drug_id, $drug_dose, $drug_unit, $drug_notes;

    public $searchService = '';
    public $available_services = [];
    public $available_items = [];
    public $available_drugs = [];

    // وحدات المنتج وقت الإضافة
    public $item_units = []; // [['id'=>..., 'name'=>...], ...]

    // ======== متغيرات التعديل للخدمة ========
    public $edit_service_row_id = null;
    public $edit_service_service_id = null;
    public $edit_service_type = null;
    public $edit_service_notes = null;

    // ======== متغيرات التعديل للصنف ========
    public $edit_item_row_id = null;
    public $edit_item_product_id = null;
    public $edit_item_quantity = null;
    public $edit_item_unit = null;
    public $edit_item_notes = null;
    public $edit_item_units = []; // نفس فكرة item_units ولكن للتعديل

    // ======== متغيرات التعديل للدواء ========
    public $edit_drug_row_id = null;
    public $edit_drug_product_id = null;
    public $edit_drug_dose = null;
    public $edit_drug_unit = null;
    public $edit_drug_notes = null;

    // قواعد بسيطة (اختيارية)
    protected $rules = [
        'service_id'   => 'nullable|integer|exists:services,id',
        'service_type' => 'nullable|in:1,2,3',
        'item_id'      => 'nullable|integer|exists:productdata,id',
        'item_quantity'=> 'nullable|numeric|min:0',
        'item_unit'    => 'nullable|string|max:100',
        'drug_id'      => 'nullable|integer|exists:productdata,id',
        'drug_dose'    => 'nullable|numeric|min:0',
        'drug_unit'    => 'nullable|string|max:100',
    ];

    public function mount($surgery_id)
    {
        $this->surgery_id = $surgery_id;
        $this->loadData();
    }

    public function updatedSearchService()
    {
        $this->available_services = Service::when($this->searchService, function ($query) {
            $query->where('name->ar', 'like', '%' . $this->searchService . '%');
        })->get();
    }

    // ====== تحميل وحدات الصنف وقت الإضافة ======
    public function updatedItemId()
    {
        $this->item_units = [];
        $this->item_unit  = null;

        if (!$this->item_id) return;

        $units = [];
        $pushUnit = function ($u) use (&$units) {
            if (!$u || empty($u->id)) return;
            $units[$u->id] = [
                'id'   => $u->id,
                'name' => $u->name_ar ?? $u->name_en ?? '',
            ];
        };

        $product = Productdata::with([
            'productUnits.unitSmall:id,name_ar,name_en',
            'productUnits.unitLarge:id,name_ar,name_en',
        ])->find($this->item_id);

        if ($product) {
            foreach ($product->productUnits ?? [] as $pu) {
                if (isset($pu->unitSmall)) $pushUnit($pu->unitSmall);
                if (isset($pu->unitLarge)) $pushUnit($pu->unitLarge);
            }
        }

        $this->item_units = array_values($units);
    }

    // ====== تحميل وحدات الصنف وقت "التعديل" ======
    public function updatedEditItemProductId()
    {
        $this->edit_item_units = [];
        $this->edit_item_unit  = null;

        if (!$this->edit_item_product_id) return;

        $units = [];
        $pushUnit = function ($u) use (&$units) {
            if (!$u || empty($u->id)) return;
            $units[$u->id] = [
                'id'   => $u->id,
                'name' => $u->name_ar ?? $u->name_en ?? '',
            ];
        };

        $product = Productdata::with([
            'productUnits.unitSmall:id,name_ar,name_en',
            'productUnits.unitLarge:id,name_ar,name_en',
        ])->find($this->edit_item_product_id);

        if ($product) {
            foreach ($product->productUnits ?? [] as $pu) {
                if (isset($pu->unitSmall)) $pushUnit($pu->unitSmall);
                if (isset($pu->unitLarge)) $pushUnit($pu->unitLarge);
            }
        }

        $this->edit_item_units = array_values($units);
    }

    public function loadData()
    {
        $this->services = SurgeryServiceItem::where('surgery_id', $this->surgery_id)->get();
        $this->items    = SurgeryItem::where('surgery_id', $this->surgery_id)->get();
        $this->drugs    = SurgeryDrug::where('surgery_id', $this->surgery_id)->get();

        $this->available_services = Service::when($this->searchService, function ($query) {
            $query->where('name->ar', 'like', '%' . $this->searchService . '%');
        })->get();

        $this->available_items = Productdata::all();
        $this->available_drugs = Drug::all();

        if ($this->item_id) {
            $this->updatedItemId();
        }
    }

    // ===== إضافة =====
    public function addService()
    {
        SurgeryServiceItem::create([
            'surgery_id'   => $this->surgery_id,
            'service_id'   => $this->service_id,
            'service_type' => $this->service_type,
            'notes'        => $this->service_notes,
            'user_id'      => Auth::id(),
        ]);
        $this->reset(['service_id', 'service_type', 'service_notes']);
        $this->loadData();
    }

    public function addItem()
    {
        SurgeryItem::create([
            'surgery_id' => $this->surgery_id,
            'item_id'    => $this->item_id,
            'quantity'   => $this->item_quantity,
            'unit'       => $this->item_unit,
            'notes'      => $this->item_notes,
            'user_id'    => Auth::id(),
        ]);
        $this->reset(['item_id', 'item_quantity', 'item_unit', 'item_notes', 'item_units']);
        $this->loadData();
    }

    public function addDrug()
    {
        SurgeryDrug::create([
            'surgery_id' => $this->surgery_id,
            'drug_id'    => $this->drug_id,
            'dose'       => $this->drug_dose,
            'unit'       => $this->drug_unit,
            'notes'      => $this->drug_notes,
            'user_id'    => Auth::id(),
        ]);
        $this->reset(['drug_id', 'drug_dose', 'drug_unit', 'drug_notes']);
        $this->loadData();
    }

    // ===== حذف =====
    public function deleteService($id)
    {
        if ($row = SurgeryServiceItem::where('surgery_id', $this->surgery_id)->find($id)) {
            $row->delete();
            // في حالة كان المتعدل هو نفس الصف، الغِ التعديل
            if ($this->edit_service_row_id === $id) {
                $this->cancelServiceEdit();
            }
            $this->loadData();
        }
    }

    public function deleteItem($id)
    {
        if ($row = SurgeryItem::where('surgery_id', $this->surgery_id)->find($id)) {
            $row->delete();
            if ($this->edit_item_row_id === $id) {
                $this->cancelItemEdit();
            }
            $this->loadData();
        }
    }

    public function deleteDrug($id)
    {
        if ($row = SurgeryDrug::where('surgery_id', $this->surgery_id)->find($id)) {
            $row->delete();
            if ($this->edit_drug_row_id === $id) {
                $this->cancelDrugEdit();
            }
            $this->loadData();
        }
    }

    // ===== بدء التعديل (Inline) =====
    public function startEditService($rowId)
    {
        $row = SurgeryServiceItem::where('surgery_id', $this->surgery_id)->find($rowId);
        if (!$row) return;

        $this->edit_service_row_id     = $row->id;
        $this->edit_service_service_id = $row->service_id;
        $this->edit_service_type       = $row->service_type;
        $this->edit_service_notes      = $row->notes;
    }

    public function startEditItem($rowId)
    {
        $row = SurgeryItem::where('surgery_id', $this->surgery_id)->with('item')->find($rowId);
        if (!$row) return;

        $this->edit_item_row_id     = $row->id;
        $this->edit_item_product_id = $row->item_id;
        $this->edit_item_quantity   = $row->quantity;
        $this->edit_item_unit       = $row->unit;
        $this->edit_item_notes      = $row->notes;

        // حمّل الوحدات المتاحة للصنف الحالي
        $this->updatedEditItemProductId();
    }

    public function startEditDrug($rowId)
    {
        $row = SurgeryDrug::where('surgery_id', $this->surgery_id)->with('drug')->find($rowId);
        if (!$row) return;

        $this->edit_drug_row_id     = $row->id;
        $this->edit_drug_product_id = $row->drug_id;
        $this->edit_drug_dose       = $row->dose;
        $this->edit_drug_unit       = $row->unit;
        $this->edit_drug_notes      = $row->notes;
    }

    // ===== حفظ التعديلات =====
    public function saveServiceEdit()
    {
        if (!$this->edit_service_row_id) return;

        $row = SurgeryServiceItem::where('surgery_id', $this->surgery_id)->find($this->edit_service_row_id);
        if (!$row) return;

        $row->update([
            'service_id'   => $this->edit_service_service_id,
            'service_type' => $this->edit_service_type,
            'notes'        => $this->edit_service_notes,
            'user_id'      => Auth::id(),
        ]);

        $this->cancelServiceEdit();
        $this->loadData();
    }

    public function saveItemEdit()
    {
        if (!$this->edit_item_row_id) return;

        $row = SurgeryItem::where('surgery_id', $this->surgery_id)->find($this->edit_item_row_id);
        if (!$row) return;

        $row->update([
            'item_id'  => $this->edit_item_product_id,
            'quantity' => $this->edit_item_quantity,
            'unit'     => $this->edit_item_unit,
            'notes'    => $this->edit_item_notes,
            'user_id'  => Auth::id(),
        ]);

        $this->cancelItemEdit();
        $this->loadData();
    }

    public function saveDrugEdit()
    {
        if (!$this->edit_drug_row_id) return;

        $row = SurgeryDrug::where('surgery_id', $this->surgery_id)->find($this->edit_drug_row_id);
        if (!$row) return;

        $row->update([
            'drug_id' => $this->edit_drug_product_id,
            'dose'    => $this->edit_drug_dose,
            'unit'    => $this->edit_drug_unit,
            'notes'   => $this->edit_drug_notes,
            'user_id' => Auth::id(),
        ]);

        $this->cancelDrugEdit();
        $this->loadData();
    }

    // ===== إلغاء التعديل (تفريغ حقول التعديل) =====
    public function cancelServiceEdit()
    {
        $this->reset(['edit_service_row_id','edit_service_service_id','edit_service_type','edit_service_notes']);
    }

    public function cancelItemEdit()
    {
        $this->reset(['edit_item_row_id','edit_item_product_id','edit_item_quantity','edit_item_unit','edit_item_notes','edit_item_units']);
    }

    public function cancelDrugEdit()
    {
        $this->reset(['edit_drug_row_id','edit_drug_product_id','edit_drug_dose','edit_drug_unit','edit_drug_notes']);
    }

    public function render()
    {
        return view('livewire.surgical.surgery-services-items');
    }
}