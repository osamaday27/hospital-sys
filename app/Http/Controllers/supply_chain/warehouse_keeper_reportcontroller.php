<?php

namespace App\Http\Controllers\supply_chain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

// Models
use App\Models\supply_chain\storekeepermodel as Keeper;
use App\Models\supply_chain\storemaindata as Store;
use App\models\User as UserModel;
use App\models\hr\Employee;

class warehouse_keeper_reportcontroller extends Controller
{
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // ===== Print =====
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('supply_chain.reports.warehouse_keeper_report.print', compact('rows', 'meta'));
        }

        // ===== Export Excel =====
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // ===== AJAX (DataTables) =====
        if ($r->wantsJson() || $r->ajax()) {
            if (!$this->hasAnyFilter($r)) {
                return response()->json([
                    'draw' => intval($r->get('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $q = $this->baseQuery($r);

            // البحث العام
            $search = $r->input('search.value') ?? $r->get('q');
            if ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('s.manual_code', 'like', "%{$search}%")
                      ->orWhere('s.name_ar', 'like', "%{$search}%")
                      ->orWhere('s.name_en', 'like', "%{$search}%")
                      ->orWhere('e.employee_code', 'like', "%{$search}%")
                      ->orWhere('e.full_name', 'like', "%{$search}%")
                      ->orWhere('u.name', 'like', "%{$search}%")
                      ->orWhere('d.name_ar', 'like', "%{$search}%")
                      ->orWhere('d.name_ne', 'like', "%{$search}%"); // ← hr_sections
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب آمن
            $orderColumnIndex = (int)($r->input('order.0.column', 6)); // created_at افتراضياً
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'store_keepers.id',          // مسلسل
                1 => 's.manual_code',             // كود المخزن
                2 => 's.name_ar',                 // اسم المخزن
                3 => 'e.employee_code',           // كود الموظف
                4 => 'e.full_name',               // الاسم
                5 => 'd.name_ar',                 // القسم
                6 => 'store_keepers.created_at',  // تاريخ الإضافة
                7 => 'store_keepers.id',          // الصلاحيات (ليس لها ترتيب منطقي)
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'store_keepers.created_at';
            $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 || $length > 1000) $length = 10;

            $rows = $q->skip($start)->take($length)->get();

            // تجهيز الرد
            $data = [];
            foreach ($rows as $row) {
                $storeName = trim(($row->s_name_ar ?? '') . ' / ' . ($row->s_name_en ?? ''));
                $empName   = $row->e_full_name ?: ($row->u_name ?: '—');

                // القسم: عربي + إنجليزي (name_ne) إن وُجد
                $deptNamePieces = [];
                if (!empty($row->d_name_ar)) $deptNamePieces[] = $row->d_name_ar;
                if (!empty($row->d_name_ne)) $deptNamePieces[] = $row->d_name_ne;
                $deptName = implode(' / ', $deptNamePieces);

                $createdAt = ($row->created_at instanceof \Carbon\Carbon ? $row->created_at : Carbon::parse($row->created_at))
                                ->format('Y-m-d H:i');

                $data[] = [
                    // 0 مسلسل يحسب بالواجهة
                    'store_manual_code' => $row->s_manual_code ?? '—',
                    'store_name'        => $storeName ?: '—',
                    'employee_code'     => $row->e_employee_code ?? '—',
                    'employee_name'     => $empName,
                    'department_name'   => ($deptName !== '') ? $deptName : '—',
                    'created_at'        => $createdAt,
                    'permissions'       => $this->formatPermissions($row), // يستخدم sk_status + بقية الأعلام
                ];
            }

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // تحميل القوائم (للفلاتر)
        $stores = Store::select('id','manual_code','name_ar','name_en')
            ->orderBy('name_ar')->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id),
                'manual_code' => $s->manual_code,
            ])->values();

        return view('supply_chain.reports.warehouse_keeper_report.index', [
            'stores' => $stores,
        ]);
    }

    /** هل هناك أي فلاتر؟ */
    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('store_id') ||
            $r->filled('date_from') ||
            $r->filled('date_to') ||
            $r->filled('q') ||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي (مع join للبحث والترتيب) */
    private function baseQuery(Request $r)
    {
        $q = Keeper::query()
            ->from('store_keepers')
            ->leftJoin('store_main_date as s', 's.id', '=', 'store_keepers.store_id')
            ->leftJoin('users as u', 'u.id', '=', 'store_keepers.user_id')
            ->leftJoin('employees as e', 'e.id', '=', 'u.employee_id')
            // استبدال departments بـ hr_sections والربط على employees.section
            ->leftJoin('hr_sections as d', 'd.id', '=', 'e.section')
            ->select([
                'store_keepers.*',
                'store_keepers.status as sk_status', // ← alias واضح لحالة الأمين
                's.manual_code as s_manual_code',
                's.name_ar as s_name_ar',
                's.name_en as s_name_en',
                'u.name as u_name',
                'e.employee_code as e_employee_code',
                'e.full_name as e_full_name',
                'd.name_ar as d_name_ar',
                'd.name_ne as d_name_ne',
            ]);

        // المتعدد: المخازن
        $stores = $r->get('store_id');
        if (is_array($stores) && count($stores)) {
            $q->whereIn('store_keepers.store_id', $stores);
        } elseif (!empty($stores)) {
            $q->where('store_keepers.store_id', $stores);
        }

        // التاريخ على created_at
        if ($r->filled('date_from')) {
            $q->whereDate('store_keepers.created_at', '>=', $r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('store_keepers.created_at', '<=', $r->get('date_to'));
        }

        return $q;
    }

    /** تطبيع القيم البوليانية/النصية إلى true/false قدر الإمكان */
    private function truthy($v): ?bool
    {
        if ($v === null) return null;
        // لو جاء كقيمة رقمية/منطقية مباشرة
        if ($v === 1 || $v === true || $v === '1') return true;
        if ($v === 0 || $v === false || $v === '0') return false;

        $s = strtolower(trim((string)$v));
        $trueSet  = ['true','yes','y','on','active','enabled','open'];
        $falseSet = ['false','no','n','off','inactive','disabled','closed'];
        if (in_array($s, $trueSet, true))  return true;
        if (in_array($s, $falseSet, true)) return false;

        // fallback
        return (bool)$v;
    }

    /** تجميع الصلاحيات كسلسلة أنيقة */
    private function formatPermissions($row): string
    {
        $perms = [];

        // أعلام الصلاحيات
        $flags = [
            'can_issue'      => 'صرف',
            'can_add'        => 'إضافة',
            'can_inventory'  => 'جرد',
            'can_report'     => 'تقارير',
            'is_main_keeper' => 'أمين رئيسي',
        ];
        foreach ($flags as $field => $label) {
            if ($this->truthy($row->{$field})) {
                $perms[] = $label;
            }
        }

        // حالة الأمين من alias sk_status
        $status = $this->truthy($row->sk_status ?? null);
        if ($status !== null) {
            $perms[] = $status ? 'مفعل' : 'غير مفعل';
        }

        return implode('، ', $perms) ?: '—';
    }

    /** إحضار كل النتائج للطباعة/التصدير */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('store_keepers.created_at', 'desc');

        // حد أمان
        $rows = $q->limit(200000)->get();

        $mapped = $rows->map(function ($row) {
            $storeName = trim(($row->s_name_ar ?? '') . ' / ' . ($row->s_name_en ?? ''));
            $empName   = $row->e_full_name ?: ($row->u_name ?: '—');

            $deptNamePieces = [];
            if (!empty($row->d_name_ar)) $deptNamePieces[] = $row->d_name_ar;
            if (!empty($row->d_name_ne)) $deptNamePieces[] = $row->d_name_ne;
            $deptName = implode(' / ', $deptNamePieces);

            $createdAt = ($row->created_at instanceof \Carbon\Carbon ? $row->created_at : Carbon::parse($row->created_at))
                            ->format('Y-m-d H:i');
            return [
                'store_manual_code' => $row->s_manual_code ?? '—',
                'store_name'        => $storeName ?: '—',
                'employee_code'     => $row->e_employee_code ?? '—',
                'employee_name'     => $empName,
                'department_name'   => ($deptName !== '') ? $deptName : '—',
                'created_at'        => $createdAt,
                'permissions'       => $this->formatPermissions($row),
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    /** معلومات الهيدر */
    private function buildMeta(Request $r, $rows)
    {
        $storeNames = [];
        if ($stores = $r->get('store_id')) {
            $ids = is_array($stores)? $stores : [$stores];
            $storeNames = Store::whereIn('id', $ids)->pluck('name_ar')->toArray();
        }

        return [
            'title'        => trans('supply_chain_trans.warehouse_keeper_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'store_names'  => $storeNames,
            'count'        => $rows->count(),
        ];
    }

    /** تصدير Excel */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('supply_chain_trans.table_store_manual_code'),
            trans('supply_chain_trans.table_store_name'),
            trans('supply_chain_trans.table_employee_code'),
            trans('supply_chain_trans.table_employee_name'),
            trans('supply_chain_trans.table_department'),
            trans('supply_chain_trans.table_created_at'),
            trans('supply_chain_trans.table_permissions'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['store_manual_code'],
                $r['store_name'],
                $r['employee_code'],
                $r['employee_name'],
                $r['department_name'],
                $r['created_at'],
                $r['permissions'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Warehouse_Keepers_' . now()->format('Ymd_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return Response::make($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
