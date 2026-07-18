<?php

namespace App\Http\Livewire\Suppliers\Settings\SuppliersData;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers\SuppliersData;
use App\Models\Suppliers\SuppliersGroup;
use App\Models\Suppliers\SupplierCompany;
use App\Models\General\Branch;
use App\Models\General\Countries;
use App\Models\General\Government;
use App\Models\General\City;

class SupplierBasicData extends Component
{
    public $supplierId;

    // الحقول الأساسية
    public $code;
    public $suppliersgroup_id;
    public $name_ar;
    public $name_en;
    public $branch_id;
    public $supplier_type = 'service';
    public $suppliercompany_id;
    public $tax_card_number;
    public $commercial_registration_number;
    public $company_field;
    public $status = 'active';

    // قوائم الموقع
    public $countries    = [];
    public $governments  = [];
    public $cities       = [];
    public $country_id;
    public $government_id;
    public $city_id;

    // وسائل الاتصال
    public $contact_methods = [];
    public $contact_type    = 'email';
    public $contact_value;

    // بيانات العلاقات
    public $supplierGroups;
    public $branches;
    public $supplierCompanies;

    // استماع لحدث تحديث المعرف من الـ child
    protected $listeners = [
        'supplierIdUpdated' => 'updateSupplierId',
    ];

    public function mount($supplierId = null)
    {
        $this->supplierId        = $supplierId;
        $this->supplierGroups    = SuppliersGroup::where('status', 'active')->get();
        $this->branches          = Branch::where('status', 'active')->get();
        $this->supplierCompanies = SupplierCompany::where('status', 'active')->get();

        // تحميل قائمة الدول
        $this->countries = Countries::pluck('name', 'id')->toArray();

        if ($supplierId) {
            $s = SuppliersData::findOrFail($supplierId);

            // تعبئة الحقول الأساسية
            $this->code                           = $s->code;
            $this->suppliersgroup_id              = $s->suppliersgroup_id;
            $this->name_ar                        = $s->name_ar;
            $this->name_en                        = $s->name_en;
            $this->branch_id                      = $s->branch_id;
            $this->supplier_type                  = $s->supplier_type;
            $this->suppliercompany_id             = $s->suppliercompany_id;
            $this->tax_card_number                = $s->tax_card_number;
            $this->commercial_registration_number = $s->commercial_registration_number;
            $this->company_field                  = $s->company_field;
            $this->status                         = $s->status;

            // وسائل الاتصال
            $this->contact_methods = is_array($s->contact_methods)
                ? $s->contact_methods
                : json_decode($s->contact_methods, true) ?? [];

            // تعبئة مواقع الدولة/المحافظة/المدينة
            $this->country_id    = $s->country_id;
            $this->government_id = $s->government_id;
            $this->city_id       = $s->city_id;

            // تحميل القوائم الفرعية
            $this->governments = Government::where('id_country', $this->country_id)
                                           ->pluck('name', 'id')->toArray();
            $this->cities      = City::where('id_government', $this->government_id)
                                     ->pluck('name', 'id')->toArray();
        }
    }

    // عندما يُرسل الحدث لتحديث المعرف
    public function updateSupplierId($id)
    {
        $this->supplierId = $id;
    }

    // عند تغيير الدولة
    public function updatedCountryId($value)
    {
        $this->government_id = null;
        $this->city_id       = null;

        $this->governments = Government::where('id_country', $value)
                                       ->pluck('name', 'id')->toArray();
        $this->cities      = [];
    }

    // عند تغيير المحافظة
    public function updatedGovernmentId($value)
    {
        $this->city_id = null;
        $this->cities  = City::where('id_government', $value)
                             ->pluck('name', 'id')->toArray();
    }

    // إضافة وسيلة اتصال
    public function addContactMethod()
    {
        if ($this->contact_type && $this->contact_value) {
            $this->contact_methods[] = [
                'type'  => $this->contact_type,
                'value' => $this->contact_value,
            ];
            $this->contact_type  = 'email';
            $this->contact_value = '';
        }
    }

    // إزالة وسيلة اتصال
    public function removeContactMethod($index)
    {
        unset($this->contact_methods[$index]);
        $this->contact_methods = array_values($this->contact_methods);
    }

    public function save()
    {
        $this->validate([
            'code'                    => 'required|string|unique:suppliers_data,code,' . $this->supplierId,
            'suppliersgroup_id'       => 'required|exists:suppliers_groups,id',
            'name_ar'                 => 'required|string',
            'name_en'                 => 'required|string',
            'branch_id'               => 'required|exists:branches,id',
            'supplier_type'           => 'required|in:service,goods,both',
            'suppliercompany_id'      => 'required|exists:supplier_companies,id',
            'status'                  => 'required|in:active,inactive',
            'country_id'              => 'required|exists:countries,id',
            'government_id'           => 'required|exists:government,id',
            'city_id'                 => 'required|exists:city,id',
        ]);

        $data = [
            'code'                            => $this->code,
            'suppliersgroup_id'               => $this->suppliersgroup_id,
            'name_ar'                         => $this->name_ar,
            'name_en'                         => $this->name_en,
            'branch_id'                       => $this->branch_id,
            'supplier_type'                   => $this->supplier_type,
            'suppliercompany_id'              => $this->suppliercompany_id,
            'tax_card_number'                 => $this->tax_card_number,
            'commercial_registration_number'  => $this->commercial_registration_number,
            'company_field'                   => $this->company_field,
            'country_id'                      => $this->country_id,
            'government_id'                   => $this->government_id,
            'city_id'                         => $this->city_id,
            'status'                          => $this->status,
            'contact_methods'                 => $this->contact_methods,
        ];

        if (! $this->supplierId) {
            $data['created_by'] = Auth::id();
        }

        $supplier = SuppliersData::updateOrCreate(
            ['id' => $this->supplierId],
            $data
        );

        // حدّث الخاصية وأرسلها للـ child
        $this->supplierId = $supplier->id;
        $this->emit('supplierIdUpdated', $this->supplierId);

        // أحداث موجودة سابقاً
        $this->dispatchBrowserEvent('supplier-saved');
        $this->emit('goToTab', 'tab2');
    }

    public function render()
    {
        return view('livewire.suppliers.settings.suppliers-data.supplier-basic-data');
    }
}
