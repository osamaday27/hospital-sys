<?php


namespace App\Http\Controllers\suppliers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\suppliers\SupplierCompany;
use App\Models\general\countries;
use App\Models\general\Government;
use App\Models\general\City;
use Yajra\DataTables\DataTables;

class suppliers_companycontroller extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(SupplierCompany::with(['country', 'government', 'city']))
                ->addColumn('action', function ($row) {
                    return '
                        <button data-id="' . $row->id . '" class="btn btn-sm btn-info details-btn" title="' . __('suppliers.details') . '">
                            <i class="ri-information-line"></i>
                        </button>
                        <a href="' . route('suppliers_company.edit', $row->id) . '" class="btn btn-sm btn-warning" title="' . __('general.edit') . '">
                            <i class="ri-edit-line"></i>
                        </a>
                        <button data-id="' . $row->id . '" class="btn btn-sm btn-danger delete-btn" title="' . __('general.delete') . '">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('suppliers.settings.suppliers_company.index');
    }

    public function show($id)
    {
        $company = SupplierCompany::with(['country','government','city'])
            ->findOrFail($id);

        return response()->json([
            'manual_code'                => $company->manual_code,
            'name_ar'                    => $company->name_ar,
            'name_en'                    => $company->name_en,
            'activity'                   => $company->activity,
            'commercial_registration_no' => $company->commercial_registration_no,
            'tax_card_no'                => $company->tax_card_no,
            'domain'                     => $company->domain,
            'phone'                      => $company->phone,
            'email'                      => $company->email,
            'fax'                        => $company->fax,
            // هذه الحقول نصية جاهزة
            'country_name'    => $company->country
                ? $company->country->getTranslation('name', app()->getLocale())
                : '-',
            'government_name' => $company->government
                ? $company->government->getTranslation('name', app()->getLocale())
                : '-',
            'city_name'       => $company->city
                ? $company->city->getTranslation('name', app()->getLocale())
                : '-',
            'notes'             => $company->notes,
            'status'            => $company->status,
        ]);
    }

    public function create()
    {
        $countries   = Countries::all();
        $governments = collect();
        $cities      = collect();

        return view('suppliers.settings.suppliers_company.create', compact('countries', 'governments', 'cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'manual_code'                => 'required|unique:supplier_companies,manual_code',
            'name_ar'                    => 'required',
            'name_en'                    => 'required',
            'activity'                   => 'nullable',
            'commercial_registration_no' => 'nullable',
            'tax_card_no'                => 'nullable',
            'domain'                     => 'nullable',
            'phone'                      => 'nullable',
            'email'                      => 'nullable|email',
            'fax'                        => 'nullable',
            'country_id'                 => 'nullable|exists:countries,id',
            'government_id'              => 'nullable|exists:government,id',
            'city_id'                    => 'nullable|exists:city,id',
            'notes'                      => 'nullable',
            'status'                     => 'required|in:active,inactive',
        ]);

        SupplierCompany::create($data);

        return redirect()
            ->route('suppliers_company.index')
            ->with('success', __('suppliers.company_created_successfully'));
    }

    public function edit($id)
{
    $company     = SupplierCompany::findOrFail($id);
    $countries   = Countries::all();
    // استخدم id_country و id_government
    $governments = Government::where('id_country', $company->country_id)->get();
    $cities      = City::where('id_government', $company->government_id)->get();

    return view('suppliers.settings.suppliers_company.edit',
                compact('company','countries','governments','cities'));
}



    public function update(Request $request, SupplierCompany $suppliers_company)
    {
        $data = $request->validate([
            'name_ar'                    => 'required',
            'name_en'                    => 'required',
            'activity'                   => 'nullable',
            'commercial_registration_no' => 'nullable',
            'tax_card_no'                => 'nullable',
            'domain'                     => 'nullable',
            'phone'                      => 'nullable',
            'email'                      => 'nullable|email',
            'fax'                        => 'nullable',
            'country_id'                 => 'nullable|exists:countries,id',
            'government_id'              => 'nullable|exists:government,id',
            'city_id'                    => 'nullable|exists:city,id',
            'notes'                      => 'nullable',
            'status'                     => 'required|in:active,inactive',
        ]);

        $suppliers_company->update($data);

        return redirect()
            ->route('suppliers_company.index')
            ->with('success', __('suppliers.company_updated_successfully'));
    }

    public function destroy(Request $request, SupplierCompany $suppliers_company)
    {
        $suppliers_company->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('suppliers.company_deleted_successfully'),
            ]);
        }

        return redirect()
            ->route('suppliers_company.index')
            ->with('success', __('suppliers.company_deleted_successfully'));
    }

    // ✅ دوال AJAX لجلب المحافظات والمدن حسب الدولة والمحافظة
    public function getGovernments($country_id)
{
    return Government::where('id_country', $country_id)->get()->map(function($g) {
        return [
            'id'   => $g->id,
            'name' => $g->getTranslation('name', app()->getLocale()),
        ];
    });
}

public function getCities($government_id)
{
    return City::where('id_government', $government_id)->get()->map(function($c) {
        return [
            'id'   => $c->id,
            'name' => $c->getTranslation('name', app()->getLocale()),
        ];
    });
}
}
