<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Suppliers\SuppliersData;
use App\Models\Suppliers\SupplierOpeningBalance;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class supplier_opening_balancescontroller extends Controller
{
    public function index(Request $request)
    {
        $suppliers = SuppliersData::select('id','code','name_ar','name_en')
            ->whereNull('deleted_at')
            ->get();

        $currentYear = date('Y');
        $years = [];
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $years[] = $y;
        }

        // الموردين المستخدمين (فقط غير المحذوفين)
        $usedSupplierIds = SupplierOpeningBalance::whereNull('deleted_at')->pluck('supplier_id')->toArray();

        if ($request->ajax()) {
            $query = SupplierOpeningBalance::with('supplier')
                ->whereNull('deleted_at')
                ->select('supplier_opening_balances.*');

            return DataTables::of($query)
                ->addColumn('supplier_ar', fn($row) => optional($row->supplier)->name_ar)
                ->addColumn('supplier_en', fn($row) => optional($row->supplier)->name_en)
                ->addColumn('supplier_code', fn($row) => optional($row->supplier)->code)
                ->editColumn('year', fn($row) => $row->year)
                ->editColumn('credit_balance', fn($row) => number_format($row->credit_balance,2))
                ->editColumn('debit_balance', fn($row) => number_format($row->debit_balance,2))
                ->addColumn('action', function($row){
                    return '
                        <button data-id="'.$row->id.'" class="btn btn-sm btn-outline-secondary edit-btn">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button data-id="'.$row->id.'" class="btn btn-sm btn-outline-danger delete-btn">
                            <i class="ri-delete-bin-line"></i>
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('suppliers.program.supplier_opening_balances.index', compact(
            'suppliers','years','usedSupplierIds'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'    => [
                'required',
                Rule::exists('suppliers_data','id'),
                Rule::unique('supplier_opening_balances')
                    ->where(fn($q) => $q->whereNull('deleted_at'))
            ],
            'year'           => 'required|integer',
            'credit_balance' => 'required|numeric',
            'debit_balance'  => 'required|numeric',
        ], [], [
            'supplier_id'    => __('suppliers.supplier_ar'),
            'year'           => __('suppliers.year'),
            'credit_balance' => __('suppliers.credit_balance'),
            'debit_balance'  => __('suppliers.debit_balance'),
        ]);

        $bal = SupplierOpeningBalance::create($data);
        $bal->load('supplier');

        return response()->json([
            'message' => __('suppliers.toast_added'),
            'data'    => [
                'id'             => $bal->id,
                'supplier_id'    => $bal->supplier->id,
                'supplier_ar'    => $bal->supplier->name_ar,
                'supplier_en'    => $bal->supplier->name_en,
                'supplier_code'  => $bal->supplier->code,
                'year'           => $bal->year,
                'credit_balance' => number_format($bal->credit_balance,2),
                'debit_balance'  => number_format($bal->debit_balance,2),
            ],
        ]);
    }

    public function edit($id)
    {
        $bal = SupplierOpeningBalance::findOrFail($id);
        return response()->json($bal);
    }

    public function update(Request $request, $id)
    {
        $bal = SupplierOpeningBalance::findOrFail($id);

        $data = $request->validate([
            'supplier_id'    => [
                'required',
                Rule::exists('suppliers_data','id'),
                Rule::unique('supplier_opening_balances')
                    ->where(fn($q) => $q->whereNull('deleted_at'))
                    ->ignore($bal->id)
            ],
            'year'           => 'required|integer',
            'credit_balance' => 'required|numeric',
            'debit_balance'  => 'required|numeric',
        ], [], [
            'supplier_id'    => __('suppliers.supplier_ar'),
            'year'           => __('suppliers.year'),
            'credit_balance' => __('suppliers.credit_balance'),
            'debit_balance'  => __('suppliers.debit_balance'),
        ]);

        $bal->update($data);
        $bal->load('supplier');

        return response()->json([
            'message' => __('suppliers.toast_updated'),
            'data'    => [
                'id'             => $bal->id,
                'supplier_id'    => $bal->supplier->id,
                'supplier_ar'    => $bal->supplier->name_ar,
                'supplier_en'    => $bal->supplier->name_en,
                'supplier_code'  => $bal->supplier->code,
                'year'           => $bal->year,
                'credit_balance' => number_format($bal->credit_balance,2),
                'debit_balance'  => number_format($bal->debit_balance,2),
            ],
        ]);
    }

    public function destroy($id)
    {
        $bal = SupplierOpeningBalance::findOrFail($id);
        $bal->delete();
        return response()->json([
            'message' => __('suppliers.toast_deleted'),
        ]);
    }
}
