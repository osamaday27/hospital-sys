<?php
namespace App\Http\Controllers\finance\contracts_management;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\finance\contract_frist_level;
use App\models\finance\price_lists;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class contract_frist_levelcontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.contracts_management.first_level.index');
    }

    public function create()
    {
        $priceLists = price_lists::all();
        return view('finance.pricing_contracts_management.contracts_management.first_level.create', compact('priceLists'));
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
                'price_list_id' => 'required|exists:fin_price_lists,id',
                'contract_start_date' => 'nullable|date',
                'contract_type' => 'required|in:cash,credit',
            ]);

            // إنشاء سجل جديد
            $contract_frist_level = new contract_frist_level();

            // تعيين القيم
            $contract_frist_level->name = ['ar' => $request->name_ar, 'en' => $request->name_en];
            $contract_frist_level->company_code = $request->company_code;
            $contract_frist_level->commercial_registration = $request->commercial_registration;
            $contract_frist_level->address = $request->address;
            $contract_frist_level->notes = $request->notes;
            $contract_frist_level->claim_due_period = $request->claim_due_period;
            $contract_frist_level->send_period = $request->send_period;
            $contract_frist_level->price_list_id = $request->price_list_id;
            $contract_frist_level->contract_start_date = $request->contract_start_date;
            $contract_frist_level->entity_type = $request->contract_type;
            // حفظ السجل
            $contract_frist_level->save();

            session()->flash('add', trans('service_settings.level_created_success'));
            return redirect()->route('management_contract_frist_level.index');
     } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $contract = contract_frist_level::findOrFail($id);
        $priceLists = price_lists::all();
        return view('finance.pricing_contracts_management.contracts_management.first_level.edit', compact('contract', 'priceLists'));
    }

   public function update(Request $request, $id)
{
    try {
        // تحقق من البيانات
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'claim_due_period' => 'nullable|integer',
            'send_period' => 'nullable|integer',
            'price_list_id' => 'required|exists:fin_price_lists,id',
            'contract_start_date' => 'nullable|date',
            'contract_type' => 'required|in:cash,credit',
        ]);

        // استدعاء السجل
        $contract_frist_level = contract_frist_level::findOrFail($id);

        // تحديث القيم
        $contract_frist_level->name = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $contract_frist_level->company_code = $request->company_code;
        $contract_frist_level->commercial_registration = $request->commercial_register;
        $contract_frist_level->address = $request->address;
        $contract_frist_level->notes = $request->notes;
        $contract_frist_level->claim_due_period = $request->claim_due_period;
        $contract_frist_level->send_period = $request->send_period;
        $contract_frist_level->price_list_id = $request->price_list_id;
        $contract_frist_level->contract_start_date = $request->contract_start_date;
        $contract_frist_level->entity_type = $request->contract_type;

        // حفظ التعديلات
        $contract_frist_level->save();

        session()->flash('edit', trans('service_settings.level_updated_success'));
        return redirect()->route('management_contract_frist_level.index');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withErrors(['error' => $e->getMessage()]);
    }
}

    public function destroy($id)
    {
        $contract = contract_frist_level::find($id);

        if ($contract) {
            $contract->delete();
            return response()->json(['status' => 'success', 'message' => 'Contract deleted successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Contract not found'], 404);
    }

    public function getcontract_frist_level()
    {
        $contracts = contract_frist_level::select(['id', 'name',  'company_code', 'commercial_registration', 'address', 'claim_due_period', 'send_period', 'price_list_id', 'contract_start_date', 'entity_type', 'created_at', 'status'])->with('priceList');;

        return DataTables::of($contracts)
            ->addColumn('status', function ($level) {
                $status = $level->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('name', function ($level) {
                return $level->getTranslation('name', app()->getLocale());
            })
            ->addColumn('price_list', function ($contract) {
                return $contract->priceList ? $contract->priceList->name : 'N/A';
            }) 
            ->editColumn('created_at', function ($contract) {
                return Carbon::parse($contract->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($contract) {
                return '<a href="' .
                    route('management_contract_frist_level.edit', $contract->id) .
                    '" class="btn btn-warning btn-sm">'. trans('buttons.edit') .'</a>
                        <a href="javascript:void(0)" onclick="deleteFunction(' .
                    $contract->id .
                    ')" class="btn btn-danger btn-sm">'. trans('buttons.delete') .'</a>';
            })
            ->rawColumns(['action','status','price_list'])
            ->make(true);
    }
}