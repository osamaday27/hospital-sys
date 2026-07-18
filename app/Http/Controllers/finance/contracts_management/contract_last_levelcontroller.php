<?php
namespace App\Http\Controllers\finance\contracts_management;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\finance\contract_second_level;
use App\models\finance\contract_last_level;
use App\models\finance\contracts_details_last_level;

use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use App\models\finance\service;
use Illuminate\Support\Facades\Crypt;

class contract_last_levelcontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.contracts_management.last_level.index');
    }

    public function create()
    {
        $contractsecondlevel = contract_second_level::all();
        return view('finance.pricing_contracts_management.contracts_management.last_level.create', compact('contractsecondlevel'));
    }
    public function creatdetails($id)
    {
        $contract_details = contracts_details_last_level::where('last_level_id', $id)->latest()->first();
        $last_level_id = $id;
        return view('finance.pricing_contracts_management.contracts_management.last_level.createdetails', compact('contract_details', 'last_level_id'));
    }
    public function financialsettings($id)
    {
        $financialsettings = contracts_details_last_level::where('last_level_id', $id)->latest()->first();
        $last_level_id = $id;
        return view('finance.pricing_contracts_management.contracts_management.last_level.financialsettings', compact('financialsettings', 'last_level_id'));
    }
    public function surgeryfinancialsettings($contract_id, $service_id)
    {
        return view('finance.pricing_contracts_management.contracts_management.last_level.surgeryfinancialsettings', compact('contract_id', 'service_id'));
    }
    
      
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'company_code' => 'nullable|string|max:50',
                'commercial_registration' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'claim_due_period' => 'nullable|integer',
                'send_period' => 'nullable|integer',
                'second_level_id' => 'required|exists:fin_contract_second_level,id',
                'contract_start_date' => 'nullable|date',
                'contract_type' => 'required|in:cash,credit',
            ]);

            // إنشاء سجل جديد
            $contract_last_level = new contract_last_level();

            // تعيين القيم
            $contract_last_level->name = ['ar' => $request->name_ar, 'en' => $request->name_en];
            $contract_last_level->company_code = $request->company_code;
            $contract_last_level->commercial_registration = $request->commercial_registration;
            $contract_last_level->address = $request->address;
            $contract_last_level->notes = $request->notes;
            $contract_last_level->claim_due_period = $request->claim_due_period;
            $contract_last_level->send_period = $request->send_period;
            $contract_last_level->second_level_id = $request->second_level_id;
            $contract_last_level->contract_start_date = $request->contract_start_date;
            $contract_last_level->entity_type = $request->contract_type;
            // حفظ السجل
            $contract_last_level->save();

            session()->flash('add', trans('service_settings.level_created_success'));
            return redirect()->route('management_contract_last_level.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $contract = contract_last_level::findOrFail($id);
        $contractsecondlevel = contract_second_level::all();
        return view('finance.pricing_contracts_management.contracts_management.last_level.edit', compact('contract', 'contractsecondlevel'));
    }

    public function update(Request $request, $id)
    {
        try {
            // التحقق من صحة البيانات
            $validated = $request->validate([
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'company_code' => 'nullable|string|max:50',
                'commercial_registration' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'claim_due_period' => 'nullable|integer',
                'send_period' => 'nullable|integer',
                'second_level_id' => 'required|exists:fin_contract_second_level,id',
                'contract_start_date' => 'nullable|date',
                'contract_type' => 'required|in:cash,credit',
            ]);

            // البحث عن السجل المطلوب تحديثه
            $contract_last_level = contract_last_level::findOrFail($id);
            // تحديث القيم
            $contract_last_level->name = ['ar' => $request->name_ar, 'en' => $request->name_en];
            $contract_last_level->company_code = $request->company_code;
            $contract_last_level->commercial_registration = $request->commercial_register;
            $contract_last_level->address = $request->address;
            $contract_last_level->notes = $request->notes;
            $contract_last_level->claim_due_period = $request->claim_due_period;
            $contract_last_level->send_period = $request->send_period;
            $contract_last_level->second_level_id = $request->second_level_id;
            $contract_last_level->contract_start_date = $request->contract_start_date;
            $contract_last_level->entity_type = $request->contract_type;

            // حفظ التعديلات
            $contract_last_level->save();

            session()->flash('update', trans('service_settings.level_updated_success'));
            return redirect()->route('management_contract_last_level.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $contract = contract_last_level::find($id);

        if ($contract) {
            $contract->delete();
            return response()->json(['status' => 'success', 'message' => 'Contract deleted successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Contract not found'], 404);
    }

    public function getcontract_last_level()
    {
        $contracts = contract_last_level::select(['id', 'name', 'company_code', 'commercial_registration', 'address', 'claim_due_period', 'send_period', 'second_level_id', 'contract_start_date', 'entity_type', 'created_at', 'status'])->with('second_level');
        return DataTables::of($contracts)
            ->addColumn('status', function ($level) {
                $status = $level->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    default:
                        return '<span class="badge bg-lastary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('name', function ($level) {
                return $level->getTranslation('name', app()->getLocale());
            })
            ->addColumn('second_level', function ($contract) {
                return $contract->second_level ? $contract->second_level->name : 'N/A';
            })
            ->editColumn('created_at', function ($contract) {
                return Carbon::parse($contract->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($contract) {
                return '
        <a href="' .
                    route('management_contract_last_level.edit', $contract->id) .
                    '" class="btn btn-warning btn-sm">
            ' .
                    trans('buttons.edit') .
                    '
        </a>
        <a href="' .
                    route('management_contract_last_level.creatdetails', $contract->id) .
                    '" class="btn btn-info btn-sm">
            ' .
                    trans('buttons.creatdetails') .
                    '
        </a>
        <a href="' .
                    route('management_contract_last_level.financialsettings', $contract->id) .
                    '" class="btn btn-success btn-sm">
            ' .
                    trans('buttons.financial_settings') .
                    '
        </a>
        <a href="javascript:void(0)" onclick="deleteFunction(' .
                    $contract->id .
                    ')" class="btn btn-danger btn-sm">
            ' .
                    trans('buttons.delete') .
                    '
        </a>';
            })

            ->rawColumns(['action', 'status', 'price_list'])
            ->make(true);
    }
    public function getServices(Request $request)
    {
        try {
            $idContract = $request->contract_id;

            // Retrieve services with prices based on the selected price list
            $services = Service::limit(50)->get();
            return DataTables::of($services)
                ->editColumn('name', fn($service) => $service->getTranslation('name', app()->getLocale()))
                ->editColumn('description', fn($service) => $service->getTranslation('description', app()->getLocale()))
                ->editColumn('level_type', fn($service) => $this->getLevelType($service))
                ->editColumn('services_not_covered', fn($service) => $this->generateservices_not_coveredField($service, $idContract, 'services_not_covered'))
                ->editColumn('services_require_approval', fn($service) => $this->generateservices_require_approvalField($service, $idContract, 'services_require_approval'))
                ->editColumn('created_at', fn($service) => Carbon::parse($service->created_at)->format('d/m/Y h:i A'))
                ->rawColumns(['services_not_covered', 'services_require_approval'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getLevelType($service)
    {
        $sublevel = $service->sublevel;
        if ($sublevel && $sublevel->level1) {
            $level1Name = $sublevel->level1->getTranslation('name', app()->getLocale());
            $sublevelName = $sublevel->getTranslation('name', app()->getLocale());
            return "$sublevelName ($level1Name)";
        }

        return 'N/A';
    }

    private function generateservices_require_approvalField($service, $idContract = null, $type = 'services_require_approval')
    {
        $fieldName = $type;
        $contract_details = contracts_details_last_level::where('last_level_id', $idContract)->latest()->first();

        $checked = in_array($service->id, json_decode($contract_details->services_require_approval ?? '[]')) ? 'checked' : '';
        return "<input type='checkbox'  class='form-check-input checkboxservicec' data-last_level_id='{$idContract}' data-service-id='{$service->id}'  data-field='{$fieldName}' ' .
            $checked .
            '>";
    }
    private function generateservices_not_coveredField($service, $idContract = null, $type = 'services_not_covered')
    {
        $fieldName = $type;
        $contract_details = contracts_details_last_level::where('last_level_id', $idContract)->latest()->first();

        $checked = in_array($service->id, json_decode($contract_details->services_not_covered ?? '[]')) ? 'checked' : '';
        return "<input type='checkbox'  class='form-check-input checkboxservicec' data-last_level_id='{$idContract}' data-service-id='{$service->id}'  data-field='{$fieldName}' ' .
            $checked .
            '>";
    }
    public function storeOrUpdatel(Request $request)
    {
        // التحقق من البيانات المدخلة
        $validated = $request->validate([
            'last_level_id' => 'nullable|exists:fin_contract_last_level,id', // التحقق من وجود last_level_id في جدول fin_contract_last_level
            'status' => 'nullable|in:active,inactive', // التحقق من قيمة الحالة
            'user_id' => 'nullable|exists:users,id', // التحقق من وجود user_id في جدول users
            'discount_percentage_all_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_all_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_clinic_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_clinic_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_inpatient_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_inpatient_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_surgical_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_surgical_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_lab_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_lab_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_emergency_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_emergency_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_rad_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_rad_services' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_medications' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_medications' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'discount_percentage_supplies' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'patient_contribution_supplies' => 'nullable|numeric', // التحقق من كون القيمة رقمية
            'services_require_approval' => 'nullable', // التحقق من كون البيانات سلسلة نصية
            'services_not_covered' => 'nullable', // التحقق من كون البيانات سلسلة نصية
        ]);

        // التحقق من وجود السجل الحالي أو إنشائه إذا لم يكن موجودًا
        $finContractDetails = contracts_details_last_level::updateOrCreate(
            ['last_level_id' => $request->last_level_id], // شرط البحث (في هذه الحالة last_level_id)
            $validated, // البيانات التي سيتم حفظها أو تحديثها
        );

        // إرجاع الرد المناسب
        return response()->json([
            'message' => 'تم حفظ البيانات بنجاح!',
            'data' => $finContractDetails,
            'status' => 'success',
        ]);
    }
}