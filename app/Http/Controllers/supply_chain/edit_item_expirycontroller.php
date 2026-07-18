<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\supply_chain\productstorebalance;
use App\models\supply_chain\productdata;
use App\Models\supply_chain\storemaindata;

class edit_item_expirycontroller extends Controller
{
    /**
     * شاشة عرض تعديل تاريخ الانتهاء
     */
    public function index()
    {
        return view('supply_chain.program.edit_item_expiry.index');
    }

    /**
     * داتا سيرفر سايد لـ DataTables
     */
    public function ajaxList(Request $request)
    {
        $start   = (int) $request->input('start', 0);
        $length  = (int) $request->input('length', 10);
        $length  = $length > 0 ? min($length, 500) : 10;

        // البحث: نأخذ q من الحقل الخارجي أو search[value] من DataTables
        $search  = trim($request->input('q', ''));
        if ($search === '') {
            $search = trim($request->input('search.value', ''));
        }

        // ترتيب
        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // ترتيب الأعمدة حسب ما هو معروض في الواجهة (نفس ترتيب الأعمدة عدا العمود الأول # والأخير)
        $orderable = [
            1 => 'product_name',
            2 => 'p.product_code',
            3 => 'psb.batch_no',
            4 => 'psb.expiry_date',
            5 => 'unit_name',
            6 => 'psb.quantity_in_base_unit',
        ];
        $orderCol = $orderable[$orderIdx] ?? 'product_name';

        // القاعدة
        $base = DB::table('product_store_balances as psb')
            ->join('products_data as p', 'p.id', '=', 'psb.product_id')
            ->leftJoin('units as u', 'u.id', '=', 'psb.units_id')
            ->whereNull('psb.deleted_at')
            ->where('psb.quantity_in_base_unit', '>', 0);

        $recordsTotal = (clone $base)->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('p.name_ar', 'like', "%{$search}%")
                    ->orWhere('p.name_en', 'like', "%{$search}%")
                    ->orWhere('p.product_code', 'like', "%{$search}%")
                    ->orWhere('psb.batch_no', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $base)->count();

        // نحدّد الأعمدة مع aliasات حتى نقدر نرتّب على product_name / unit_name
        $query = $base->select([
            'psb.id as id', // ← أضف هذا
            'psb.product_id',
            'psb.store_id',
            DB::raw('COALESCE(p.name_ar, p.name_en) as product_name'),
            'p.product_code',
            'psb.batch_no',
            DB::raw("DATE_FORMAT(psb.expiry_date, '%Y-%m-%d') as expiry_date"),
            DB::raw('COALESCE(u.name_ar, u.name_en) as unit_name'),
            'psb.quantity_in_base_unit',
        ]);

        // تطبيق الترتيب
        switch ($orderCol) {
            case 'product_name':
                $query->orderBy(DB::raw('product_name'), $orderDir);
                break;
            case 'unit_name':
                $query->orderBy(DB::raw('unit_name'), $orderDir);
                break;
            case 'p.product_code':
                $query->orderBy('p.product_code', $orderDir);
                break;
            case 'psb.batch_no':
                $query->orderBy('psb.batch_no', $orderDir);
                break;
            case 'psb.expiry_date':
                $query->orderBy('psb.expiry_date', $orderDir);
                break;
            case 'psb.quantity_in_base_unit':
                $query->orderBy('psb.quantity_in_base_unit', $orderDir);
                break;
            default:
                $query->orderBy(DB::raw('product_name'), $orderDir);
                break;
        }

        $rows = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ]);
    }


    /**
     * حفظ تاريخ الانتهاء لسطر رصيد محدد
     */
    public function update(Request $request)
    {
        $rules = [
            'id'          => ['required', 'integer', Rule::exists((new productstorebalance)->getTable(), 'id')],
            'expiry_date' => ['nullable', 'date'], // لو فاضي = إزالة التاريخ
        ];
        Validator::make($request->all(), $rules)->validate();

        /** @var productstorebalance $row */
        $row = productstorebalance::findOrFail((int)$request->id);

        // عيّن التاريخ أو null عند الإزالة
        $newDate = $request->filled('expiry_date') ? Carbon::parse($request->expiry_date)->format('Y-m-d') : null;

        $row->expiry_date = $newDate;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => __('supply_chain_trans.updated_success') ?? 'تم التحديث بنجاح',
            'expiry'  => $newDate,
        ]);
    }
}
