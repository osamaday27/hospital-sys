<?php

namespace App\Http\Controllers\suppliers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

// Models
use App\Models\Suppliers\suppliersdata as Supplier;
use App\Models\Suppliers\SupplierInvoice as Invoice;
use App\Models\Suppliers\SupplierPaymentInstallment as Installment;
use App\Models\Finance\ChequeLeaf as ChequeLeaf;

class supplier_payment_reportcontroller extends Controller
{
    /** Index: HTML view, AJAX (DataTables), print, export_excel */
    public function index(Request $r)
    {
        $action = $r->get('action'); // print | export_excel | (ajax|html)

        // Print
        if ($action === 'print') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return view('suppliers.reports.supplier_payment_report.print', compact('rows','meta'));
        }

        // Export Excel
        if ($action === 'export_excel') {
            [$rows, $meta] = $this->fetchAllForReport($r);
            return $this->exportExcel($rows, $meta);
        }

        // AJAX (DataTables)
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

            // بحث عام
            $search = $r->input('search.value') ?? $r->get('q');
            if ($search) {
                $q->where(function($w) use ($search){
                    $w->where('spi.reference_no', 'like', "%{$search}%")
                      ->orWhere('sd.name_ar', 'like', "%{$search}%")
                      ->orWhere('sd.name_en', 'like', "%{$search}%")
                      ->orWhere('cl.cheque_number', 'like', "%{$search}%")
                      ->orWhere('inv.invoice_no', 'like', "%{$search}%");
                });
            }

            $totalFiltered = (clone $q)->count();

            // ترتيب
            $orderColumnIndex = (int)($r->input('order.0.column', 2)); // التاريخ افتراضيًا
            $orderDir         = strtolower($r->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'spi.id',
                1 => 'spi.reference_no',
                2 => 'spi.paid_date',
                3 => 'sd.name_ar',
                4 => 'spi.amount',    // شيك (تقريبي)
                5 => 'cl.cheque_number',
                6 => 'spi.amount',    // نقدي (تقريبي)
                7 => 'spi.amount',    // استقطاع
                8 => 'spi.amount',    // تسوية إشعارات
            ];
            $orderBy = $orderMap[$orderColumnIndex] ?? 'spi.paid_date';
            $q->orderBy($orderBy, $orderDir);

            // تقسيم الصفحات
            $start  = (int)$r->get('start', 0);
            $length = (int)$r->get('length', 10);
            if ($length < 1 && $length !== -1) $length = 10;

            if ($length === -1) {
                $rows = $q->get();
            } else {
                $rows = $q->skip($start)->take($length)->get();
            }

            $data = [];
            foreach ($rows as $row) {
                // توزيع المبالغ حسب method
                [$chequeAmt, $cashAmt, $deductAmt, $creditNoteAmt] = $this->splitAmountsByMethod(
                    $row->method, (float)$row->amount
                );

                $receiptNo = $row->reference_no ?: ($row->cheque_number ?: $row->id);
                $chequeNo  = stripos((string)$row->method, 'cheque') !== false
                           ? ($row->cheque_number ?: $row->reference_no ?: '—')
                           : '—';

                $data[] = [
                    'receipt_no'   => $receiptNo,
                    'paid_date'    => $row->paid_date ? Carbon::parse($row->paid_date)->format('Y-m-d') : '—',
                    'supplier'     => $row->supplier_name,
                    'cheque_amount'=> number_format($chequeAmt, 2),
                    'cheque_no'    => $chequeNo,
                    'cash_amount'  => number_format($cashAmt, 2),
                    'deduction'    => number_format($deductAmt, 2),
                    'credit_note'  => number_format($creditNoteAmt, 2),
                ];
            }

            return response()->json([
                'draw'            => intval($r->get('draw')),
                'recordsTotal'    => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data'            => $data,
            ]);
        }

        // تحميل قائمة الموردين للفلاتر
        $suppliers = Supplier::select('id','name_ar','name_en','code')
            ->orderBy('name_ar')->limit(5000)->get()
            ->map(fn($s)=>[
                'id'=>$s->id,
                'name'=> ($s->name_ar ?: $s->name_en ?: (string)$s->id) . ($s->code ? " - {$s->code}" : ''),
            ])->values();

        return view('suppliers.reports.supplier_payment_report.index', compact('suppliers'));
    }

    /** فلاتر مفعلة؟ */
    private function hasAnyFilter(Request $r): bool
    {
        return (bool)(
            $r->filled('date_from') ||
            $r->filled('date_to')   ||
            $r->filled('suppliers') ||
            $r->filled('receipt_no')||
            ($r->input('search.value'))
        );
    }

    /** الاستعلام الأساسي */
    private function baseQuery(Request $r)
    {
        $q = Installment::from('supplier_payment_installments as spi')
            ->join('supplier_invoices as inv','inv.id','=','spi.supplier_invoice_id')
            ->join('suppliers_data as sd','sd.id','=','inv.supplier_id')
            ->leftJoin('fin_cheque_leaves as cl','cl.id','=','spi.cheque_leaf_id')
            ->whereNotNull('spi.paid_date')  // حركات مسددة فقط
            ->select([
                'spi.id',
                'spi.reference_no',
                'spi.method',
                'spi.amount',
                'spi.paid_date',
                'sd.id as supplier_id',
                DB::raw("COALESCE(sd.name_ar, sd.name_en, CONCAT('#',sd.id)) as supplier_name"),
                'cl.cheque_number',
            ]);

        // التاريخ (على paid_date)
        if ($r->filled('date_from')) {
            $q->whereDate('spi.paid_date','>=',$r->get('date_from'));
        }
        if ($r->filled('date_to')) {
            $q->whereDate('spi.paid_date','<=',$r->get('date_to'));
        }

        // المورد
        $suppliers = $r->get('suppliers');
        if (is_array($suppliers) && count($suppliers)) {
            $q->whereIn('inv.supplier_id', $suppliers);
        } elseif (!empty($suppliers)) {
            $q->where('inv.supplier_id', $suppliers);
        }

        // رقم الإيصال
        if ($rec = $r->get('receipt_no')) {
            $q->where(function($w) use ($rec) {
                $w->where('spi.reference_no','like',"%{$rec}%")
                  ->orWhere('cl.cheque_number','like',"%{$rec}%");
            });
        }

        return $q;
    }

    /** تقسيم مبلغ الحركة حسب نوعها */
    private function splitAmountsByMethod(?string $method, float $amount): array
    {
        $m = strtolower((string)$method);

        $isCheque = str_contains($m, 'cheque') || str_contains($m,'check') || str_contains($m,'شيك');
        $isCash   = str_contains($m, 'cash')   || str_contains($m,'locker') || str_contains($m,'نقد');
        $isDeduct = str_contains($m, 'deduct') || str_contains($m,'discount') || str_contains($m,'استقطاع');
        $isCN     = str_contains($m, 'credit') || str_contains($m,'alert') || str_contains($m,'settle') || str_contains($m,'إشعار');

        $cheque = $isCheque ? $amount : 0.0;
        $cash   = $isCash   ? $amount : 0.0;
        $deduct = $isDeduct ? abs($amount) : 0.0;
        $cn     = $isCN     ? $amount : 0.0;

        return [$cheque, $cash, $deduct, $cn];
    }

    /** إحضار كل النتائج للطباعة/التصدير */
    private function fetchAllForReport(Request $r): array
    {
        if (!$this->hasAnyFilter($r)) {
            return [collect(), $this->buildMeta($r, collect())];
        }

        $q = $this->baseQuery($r)->orderBy('spi.paid_date','desc');
        $rows = $q->limit(200000)->get();

        $mapped = $rows->map(function($row){
            [$chequeAmt, $cashAmt, $deductAmt, $creditNoteAmt] = $this->splitAmountsByMethod(
                $row->method, (float)$row->amount
            );
            $receiptNo = $row->reference_no ?: ($row->cheque_number ?: $row->id);
            $chequeNo  = stripos((string)$row->method, 'cheque') !== false
                       ? ($row->cheque_number ?: $row->reference_no ?: '—')
                       : '—';

            return [
                'receipt_no'    => $receiptNo,
                'paid_date'     => $row->paid_date ? Carbon::parse($row->paid_date)->format('Y-m-d') : '—',
                'supplier'      => $row->supplier_name,
                'cheque_amount' => number_format($chequeAmt, 2),
                'cheque_no'     => $chequeNo,
                'cash_amount'   => number_format($cashAmt, 2),
                'deduction'     => number_format($deductAmt, 2),
                'credit_note'   => number_format($creditNoteAmt, 2),
            ];
        });

        return [$mapped, $this->buildMeta($r, $mapped)];
    }

    /** ميتا الهيدر */
    private function buildMeta(Request $r, $rows)
    {
        $supplierNames = [];
        if ($suppliers = $r->get('suppliers')) {
            $ids = is_array($suppliers)? $suppliers : [$suppliers];
            $supplierNames = Supplier::whereIn('id', $ids)->limit(200)->pluck('name_ar')->toArray();
        }

        return [
            'title'        => trans('suppliers.supplier_payment_report_title'),
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
            'date_from'    => $r->get('date_from'),
            'date_to'      => $r->get('date_to'),
            'receipt_no'   => $r->get('receipt_no'),
            'supplier_names'=> $supplierNames,
            'count'        => $rows->count(),
        ];
    }

    /** تصدير إكسل */
    private function exportExcel($rows, $meta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            trans('suppliers.supplier_payment_report_th_receipt'),
            trans('suppliers.supplier_payment_report_th_date'),
            trans('suppliers.supplier_payment_report_th_supplier'),
            trans('suppliers.supplier_payment_report_th_cheque_amount'),
            trans('suppliers.supplier_payment_report_th_cheque_no'),
            trans('suppliers.supplier_payment_report_th_cash_amount'),
            trans('suppliers.supplier_payment_report_th_deduction'),
            trans('suppliers.supplier_payment_report_th_credit_settle'),
        ];

        $rowIndex = 1;
        $sheet->fromArray([$headers], NULL, 'A' . $rowIndex);
        $rowIndex++;

        foreach ($rows as $r) {
            $sheet->fromArray([[
                $r['receipt_no'],
                $r['paid_date'],
                $r['supplier'],
                $r['cheque_amount'],
                $r['cheque_no'],
                $r['cash_amount'],
                $r['deduction'],
                $r['credit_note'],
            ]], NULL, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A','H') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Supplier_Payments_' . now()->format('Ymd_His') . '.xlsx';

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
