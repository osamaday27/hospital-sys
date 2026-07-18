<div class="container-fluid">
  {{-- رأس المحضر --}}
  <div class="row g-3">
    <div class="col-md-3">
      <div class="border p-2 rounded">
        <div class="text-muted small">{{ trans('supply_chain_trans.col_code') }}</div>
        <div class="fw-semibold">{{ $doc->code }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border p-2 rounded">
        <div class="text-muted small">{{ trans('supply_chain_trans.col_date') }}</div>
        <div class="fw-semibold">{{ $date }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border p-2 rounded">
        <div class="text-muted small">{{ trans('supply_chain_trans.col_supplier') }}</div>
        <div class="fw-semibold">{{ $supplier }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border p-2 rounded">
        <div class="text-muted small">{{ trans('supply_chain_trans.col_store') }}</div>
        <div class="fw-semibold">{{ $store }}</div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-md-3">
      <div class="border p-2 rounded">
        <div class="text-muted small">{{ __('رقم أمر التوريد') }}</div>
        <div class="fw-semibold">{{ $po }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border p-2 rounded">
        <div class="text-muted small">{{ __('حالة المحضر') }}</div>
        @php
          $status = $doc->status ?? 'pending';
          $badge  = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning text-dark');
        @endphp
        <span class="badge bg-{{ $badge }}">{{ trans('supply_chain_trans.status_'.$status) ?? $status }}</span>
      </div>
    </div>

    @php
      $hasNotes   = filled($doc->notes);
      $hasReceipt = filled($doc->receipt_notes);
      $notesCol   = ($hasNotes && $hasReceipt) ? 'col-md-3' : 'col-md-6';
    @endphp

    @if($hasNotes)
      <div class="{{ $notesCol }}">
        <div class="border p-2 rounded">
          <div class="text-muted small">{{ trans('supply_chain_trans.col_notes') ?? __('ملاحظات') }}</div>
          <div class="fw-normal" style="white-space: pre-wrap">{{ $doc->notes }}</div>
        </div>
      </div>
    @endif

    @if($hasReceipt)
      <div class="{{ $notesCol }}">
        <div class="border p-2 rounded">
          <div class="text-muted small">{{ trans('supply_chain_trans.col_receipt_notes') ?? __('ملاحظات الاستلام') }}</div>
          <div class="fw-normal" style="white-space: pre-wrap">{{ $doc->receipt_notes }}</div>
        </div>
      </div>
    @endif
  </div>

  <hr>

  {{-- تفاصيل الأصناف --}}
  <h6 class="mb-2">{{ trans('supply_chain_trans.items_title') }}</h6>
  @php
    $showCost = $doc->items->contains(fn($row) => !is_null($row->cost_price));
  @endphp
  <div class="table-responsive">
    <table class="table table-sm table-bordered align-middle">
      <thead class="table-light">
        <tr class="text-center">
          <th>#</th>
          <th>{{ trans('supply_chain_trans.col_item_code') }}</th>
          <th>{{ trans('supply_chain_trans.col_item_name') }}</th>
          <th>{{ __('رقم التشغيل') }}</th>
          <th>{{ __('تاريخ الانتهاء') }}</th>
          <th>{{ __('الوحدة (أمر التوريد)') }}</th>
          <th>{{ __('الوحدة المُعتمدة للقبول') }}</th>
          <th>{{ __('المُورّد (عرض)') }}</th>
          <th>{{ __('المقبول (عرض)') }}</th>
          <th>{{ __('المرفوض (عرض)') }}</th>
          @if($showCost)
            <th>{{ trans('supply_chain_trans.col_cost') ?? __('التكلفة') }}</th>
          @endif
          <th>{{ trans('supply_chain_trans.col_rejection_reason') ?? __('سبب الرفض') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($doc->items as $i => $it)
          @php
            // كود واسم المنتج (مع بدائل)
  $pCode = $it->product->system_code
           ?? $it->product->barcode
           ?? ('#'.$it->product_id);
  $pName = $it->product->name_ar ?? $it->product->name_en ?? '';

            // تاريخ الانتهاء
            $exp = $it->expiry_date ? \Illuminate\Support\Carbon::parse($it->expiry_date)->format('Y-m-d') : '-';

            // اسم وحدة الـ PO من الـ ProductUnit (large/small)
            $poUnitName = $it->unit?->largeUnit?->name_ar
                          ?? $it->unit?->largeUnit?->name_en
                          ?? $it->unit?->smallUnit?->name_ar
                          ?? $it->unit?->smallUnit?->name_en
                          ?? '-';

            // الوحدة المعتمدة للقبول
            $acceptedUnitName = $it->acceptedUnit?->name_ar ?? $it->acceptedUnit?->name_en ?? '-';

            // كميات العرض (مخزَّنة)
            $delDisp = is_null($it->delivered_qty_display) ? 0 : $it->delivered_qty_display;
            $accDisp = is_null($it->accepted_qty_display)  ? 0 : $it->accepted_qty_display;
            $rejDisp = is_null($it->rejected_qty_display)  ? 0 : $it->rejected_qty_display;
          @endphp
          <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td class="text-nowrap">{{ $pCode }}</td>
            <td class="text-nowrap">{{ $pName }}</td>
            <td class="text-nowrap">{{ $it->batch_no ?? '-' }}</td>
            <td class="text-nowrap">{{ $exp }}</td>
            <td class="text-nowrap">{{ $poUnitName }}</td>
            <td class="text-nowrap">{{ $acceptedUnitName }}</td>

            <td class="text-end">{{ number_format($delDisp, 2) }}</td>
            <td class="text-end">{{ number_format($accDisp, 2) }}</td>
            <td class="text-end">{{ number_format($rejDisp, 2) }}</td>

            @if($showCost)
              <td class="text-end">{{ number_format($it->cost_price ?? 0, 2) }}</td>
            @endif

            <td class="text-nowrap">{{ $it->rejection_reason ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ 10 + ($showCost ? 1 : 0) }}" class="text-center text-muted">
              {{ trans('supply_chain_trans.no_items') }}
            </td>
          </tr>
        @endforelse


      </tbody>
    </table>
    @if($hasCost)
  <div class="d-flex justify-content-end mt-2">
    <div class="border rounded p-2" style="min-width: 260px;">
      <div class="text-muted small">{{ __('إجمالي التكلفة') }}</div>
      <div class="fw-semibold">{{ number_format($totalCost, 2) }}</div>
    </div>
  </div>
@endif

  </div>

  {{-- جدول الموافقات --}}
  <h6 class="mt-4">{{ trans('supply_chain_trans.approvals_title') }}</h6>
  <div class="table-responsive">
    <table class="table table-sm table-bordered">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>{{ trans('supply_chain_trans.col_employee') }}</th>
          <th>{{ trans('supply_chain_trans.col_status') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($doc->approvals as $j => $ap)
          @php
            $emp = $ap->employee;
            $nameAr = trim(implode(' ', array_filter([
              $emp->first_name_ar ?? null, $emp->second_name_ar ?? null,
              $emp->third_name_ar ?? null, $emp->fourth_name_ar ?? null
            ])));
            $nameEn = trim(implode(' ', array_filter([
              $emp->first_name_en ?? null, $emp->second_name_en ?? null,
              $emp->third_name_en ?? null, $emp->fourth_name_en ?? null
            ])));
            $empName = $emp->full_name ?? ($nameAr ?: ($nameEn ?: '-'));

            $st = $ap->status ?? 'pending';
            $b  = $st === 'approved' ? 'success' : ($st === 'rejected' ? 'danger' : 'warning text-dark');
          @endphp
          <tr>
            <td>{{ $j + 1 }}</td>
            <td class="text-nowrap">{{ $empName }}</td>
            <td><span class="badge bg-{{ $b }}">{{ trans('supply_chain_trans.status_'.$st) ?? $st }}</span></td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center text-muted">{{ __('لا توجد موافقات بعد') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
