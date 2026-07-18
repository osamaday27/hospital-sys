<?php

namespace App\Http\Controllers\Suppliers;

use Illuminate\Http\Request;
use App\models\general\branch;
use App\Http\Controllers\Controller;
use App\Models\suppliers\suppliersdata;
use App\Models\suppliers\suppliersgroup;
use Yajra\DataTables\Facades\DataTables;
use App\Models\suppliers\suppliercompany;

class suppliers_datacontroller extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = suppliersdata::with(['branch','creator'])
                ->select([
                    'id',
                    'name_ar',
                    'name_en',
                    'code',
                    'branch_id',
                    'created_by',
                    'status',
                    'created_at',
                    'updated_at',
                ]);

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addIndexColumn()
            ->addColumn('supplier_name', function($s) {
                return "{$s->name_ar} / {$s->name_en}";
            })
            ->filterColumn('supplier_name', function($q, $keyword) {
                $like = "%{$keyword}%";
                $q->where(function($q2) use($like) {
                    $q2->where('name_ar', 'like', $like)
                       ->orWhere('name_en', 'like', $like);
                });
            })
                ->addColumn('branch_name', fn($s)=> $s->branch?->name ?? '-')
                ->addColumn('created_by_name', fn($s)=> $s->creator?->name  ?? '-')
                ->addColumn('status_label', fn($s)=>
                    $s->status==='active'
                        ? __('suppliers.active')
                        : __('suppliers.inactive')
                )
                ->editColumn('created_at', fn($s)=>
                    $s->created_at->format('Y-m-d\TH:i:s')
                )
                ->editColumn('updated_at', fn($s)=>
                    $s->updated_at->format('Y-m-d\TH:i:s')
                )
                ->addColumn('action', function($row){
                    $view = '<button data-id="'.$row->id.'" class="btn btn-sm btn-info view-btn">'.__('suppliers.view').'</button>';
                    $edit = '<a href="'.route('suppliers_data.create',['id'=>$row->id]).'" class="btn btn-sm btn-warning">'.__('general.edit').'</a>';
                    $del  = '<button data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">'.__('general.delete').'</button>';
                    return "{$view} {$edit} {$del}";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('suppliers.settings.suppliers_data.index');
    }
    public function show($id)
{
    $s = SuppliersData::with([
            'branch',
            'creator',
            'group',
            'company',
            'country',
            'government',
            'city'
        ])->findOrFail($id);

    $data = [
        trans('suppliers.code')                         => $s->code,
        trans('suppliers.branch')                       => $s->branch->name         ?? '-',
        trans('suppliers.created_by')                   => $s->creator->name        ?? '-',
        trans('inpatient_trans.created_at')             => $s->created_at->format('Y-m-d\TH:i:s'),
        trans('inpatient_trans.updated_at')             => $s->updated_at->format('Y-m-d\TH:i:s'),
        trans('inpatient_trans.status')                 => $s->status === 'active'
                                                             ? __('suppliers.active')
                                                             : __('suppliers.inactive'),
        trans('suppliers.name_ar')                      => $s->name_ar,
        trans('suppliers.name_en')                      => $s->name_en,
        trans('suppliers.group')                        => $s->group->name_ar       ?? '-',
        trans('suppliers.company')                      => $s->company->name_ar     ?? '-',
        trans('suppliers.supplier_type')                => $s->supplier_type,
        trans('suppliers.tax_card_number')              => $s->tax_card_number,
        trans('suppliers.commercial_registration_number') => $s->commercial_registration_number,
        trans('suppliers.company_field')                => $s->company_field,
        trans('suppliers.contact_methods')              => collect($s->contact_methods)
                                                              ->map(fn($item) =>
                                                                  trans('suppliers.' . $item['type'])
                                                                  . ': ' . $item['value']
                                                              )
                                                              ->implode(', '),
        trans('suppliers.country')                      => $s->country->name       ?? '-',
        trans('suppliers.governorate')                  => $s->government->name    ?? '-',
        trans('suppliers.city')                         => $s->city->name          ?? '-',
    ];

    return response()->json($data);
}

    public function create($id = null)
    {
        $supplier = $id ? suppliersdata::findOrFail($id) : new suppliersdata;
        $groups   = suppliersgroup::pluck('name_ar','id');
        $branches = branch::pluck('name','id');
        $companies =suppliercompany::pluck('name_ar','id');

        return view('suppliers.settings.suppliers_data.create', compact(
            'supplier','groups','branches','companies'
        ));
    }

    public function destroy($id)
    {
        try {
            $supplier = suppliersdata::findOrFail($id);
            $supplier->delete();
            return response()->json([
                'success' => true,
                'message' => __('suppliers.deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('suppliers.delete_failed')
            ]);
        }
    }
}
