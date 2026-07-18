@extends('layouts.master_table')
@section('title') {{ __('assets.disposal.show_title') }} @stop

@section('content')
@include('assets.programs.disposal.flash')

{{-- Breadcrumb + back --}}
<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <ol class="breadcrumb m-0">
      <li class="breadcrumb-item">
        <a href="{{ route('assets.programs.disposal.index') }}">{{ __('طلبات التخلص/الشطب') }}</a>
      </li>
      <li class="breadcrumb-item active">{{ $row->request_no }}</li>
    </ol>
    <a href="{{ route('assets.programs.disposal.index') }}" class="btn btn-light">{{ __('عودة للقائمة') }}</a>
  </div>
</div>

{{-- رأس الصفحة + أزرار عامة --}}
<div class="card mb-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-1">{{ __('assets.disposal.request_no') }}: {{ $row->request_no }}</h5>
      <div class="text-muted">
        {{ __('assets.disposal.method') }}: {{ __('assets.disposal.method_'.$row->method) }} |
        {{ __('assets.disposal.status') }}: {{ __('assets.disposal.status_'.$row->status) }}
      </div>
    </div>
    @include('assets.programs.disposal._actions',['row'=>$row])
  </div>
</div>

{{-- بنود الطلب --}}
<div class="card mb-3">
  <div class="card-header">{{ __('assets.disposal.items') }}</div>
  <div class="card-body p-0">
    <table class="table table-bordered mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('assets.asset') }}</th>
          <th>{{ __('assets.qty_requested') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($row->items as $i=>$it)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $it->asset?->code }} — {{ $it->asset?->name_ar ?? $it->asset?->name_en }}</td>
            <td>{{ $it->qty_requested }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- مسار الاعتماد + إجراءات الاعتماد/الرفض لكل مستوى --}}
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>{{ __('مسار الاعتماد والقرارات') }}</span>
    <a href="{{ route('assets.programs.disposal.print.committee',$row->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
      <i class="ri-printer-line"></i> {{ __('طباعة') }}
    </a>
  </div>
  <div class="card-body p-0">
    <table class="table table-bordered mb-0 align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('اللجنة/الدور') }}</th>
          <th>{{ __('المخوَّل') }}</th>
          <th>{{ __('القرار') }}</th>
          <th>{{ __('المعتمد') }}</th>
          <th>{{ __('تاريخ القرار') }}</th>
          <th>{{ __('ملاحظات') }}</th>
          <th style="width:200px">{{ __('إجراء') }}</th>
        </tr>
      </thead>
      <tbody>
        @php
          $rows = $row->approvals->sortBy('level');
          $badgeClass = [
            'pending'  => 'badge bg-secondary',
            'approved' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
            'returned' => 'badge bg-warning text-dark',
          ];
        @endphp

        @forelse($rows as $ap)
          <tr id="ap-row-{{ $ap->id }}">
            <td>{{ $ap->level }}</td>
            <td>{{ $ap->role_or_committee ?: '-' }}</td>
            <td>{{ $ap->assigneeUser?->name ?: '-' }}</td>
            <td>
              <span class="{{ $badgeClass[$ap->decision] ?? 'badge bg-secondary' }}">
                {{ __('assets.disposal.a_'.$ap->decision) }}
              </span>
            </td>
            <td>{{ $ap->approverUser?->name ?: '-' }}</td>
            <td>{{ $ap->decided_at ? \Carbon\Carbon::parse($ap->decided_at)->format('Y-m-d H:i') : '-' }}</td>
            <td style="max-width:280px">{{ $ap->decision_notes ?: '-' }}</td>
            <td>
              @if(
                in_array($row->status,['submitted','under_review']) &&
                $ap->decision==='pending' &&
                $ap->assignee_user_id &&
                $ap->assignee_user_id == auth()->id()
              )
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-success js-open-decision"
                          data-level="{{ $ap->level }}" data-row-id="{{ $ap->id }}" data-decision="approved">
                    <i class="ri-check-line"></i> {{ __('اعتماد') }}
                  </button>
                  <button class="btn btn-outline-danger js-open-decision"
                          data-level="{{ $ap->level }}" data-row-id="{{ $ap->id }}" data-decision="rejected">
                    <i class="ri-close-line"></i> {{ __('رفض') }}
                  </button>
                </div>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted p-3">{{ __('لا توجد بيانات اعتماد بعد.') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- المرفقات --}}
@if($row->attachments->count())
<div class="card mb-3">
  <div class="card-header">{{ __('المرفقات') }}</div>
  <div class="card-body p-0">
    <table class="table table-bordered mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('الملف') }}</th>
          <th>{{ __('الملاحظات') }}</th>
          <th>{{ __('تاريخ الرفع') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($row->attachments as $i=>$att)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>
              {{-- حسب طلبك: عرض بدون Storage بهذه الصيغة /attachments/... --}}
              <a href="{{ url( ltrim($att->file_path, '/')) }}" target="_blank">
                <i class="ri-attachment-2"></i> {{ $att->original_name ?? basename($att->file_path) }}
              </a>
            </td>
            <td>{{ $att->notes ?: '-' }}</td>
            <td>{{ $att->created_at?->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- تفاصيل التنفيذ (إن وُجدت) --}}
@if($row->disposal)
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>{{ __('تفاصيل التنفيذ') }}</span>
    <a href="{{ route('assets.programs.disposal.print.execution',$row->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
      <i class="ri-printer-line"></i> {{ __('طباعة') }}
    </a>
  </div>
  <div class="card-body">
    @if($row->method==='sale')
      <div class="row g-3">
        <div class="col-md-3"><strong>{{ __('المشتري') }}:</strong> {{ $row->disposal->buyer_name }}</div>
        <div class="col-md-3"><strong>{{ __('رقم فاتورة البيع') }}:</strong> {{ $row->disposal->sale_invoice_no }}</div>
        <div class="col-md-3"><strong>{{ __('قيمة البيع') }}:</strong> {{ $row->disposal->sale_amount }}</div>
        <div class="col-md-3"><strong>{{ __('الضريبة') }}:</strong> {{ $row->disposal->vat_amount }}</div>
      </div>
    @elseif($row->method==='donation')
      <div class="row g-3">
        <div class="col-md-4"><strong>{{ __('الجهة المتبرع لها') }}:</strong> {{ $row->disposal->donee_name }}</div>
        <div class="col-md-4"><strong>{{ __('مستند التبرع') }}:</strong> {{ $row->disposal->donation_doc_no }}</div>
      </div>
    @else
      <div class="row g-3">
        <div class="col-md-4"><strong>{{ __('طريقة الإتلاف') }}:</strong> {{ $row->disposal->destruction_method }}</div>
        <div class="col-md-4"><strong>{{ __('مستند الإتلاف') }}:</strong> {{ $row->disposal->destruction_doc_no }}</div>
        <div class="col-md-4"><strong>{{ __('شهود الإتلاف') }}:</strong> {{ $row->disposal->witnessed_by }}</div>
      </div>
    @endif
  </div>
</div>
@endif

{{-- تعيين المخوّلين (draft فقط) --}}
@if($row->status === 'draft')
  <div class="card">
    <div class="card-header">{{ __('تعيين المخوّلين لكل مستوى') }}</div>
    <div class="card-body">
      <form method="post" action="{{ route('assets.programs.disposal.submit',$row->id) }}" class="js-disable-on-submit">
        @csrf
        <div class="row g-3">
          @foreach(($levels ?? ['لجنة']) as $idx=>$lbl)
            @php $level = $idx+1; @endphp
            <div class="col-md-6">
              <label class="form-label">{{ $lbl }} (Level {{ $level }})</label>
              <select name="assignees[{{ $level }}]" class="form-select js-user"></select>
            </div>
          @endforeach
        </div>
        <div class="mt-3 d-flex justify-content-end">
          <button class="btn btn-success"><i class="ri-send-plane-line"></i> {{ __('assets.disposal.submit') }}</button>
        </div>
      </form>
    </div>
  </div>

  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      $('.js-user').select2({
        theme:'bootstrap-5',
        allowClear:true,
        placeholder:'{{ __("اختر المخوّل") }}',
        ajax:{
          url:'{{ route("assets.programs.disposal.ajax.users") }}',
          dataType:'json', delay:250,
          data:params=>({q:params.term}),
          processResults:data=>({results:data})
        }
      });
    });
  </script>
@endif

{{-- Modal قرار اعتماد/رفض --}}
<div class="modal fade" id="decisionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="{{ route('assets.programs.disposal.approve',$row->id) }}" class="modal-content js-disable-on-submit">
      @csrf
      {{-- المهم: الكنترولر يتوقع level وليس approval_id --}}
      <input type="hidden" name="level" id="appr-level">
      <input type="hidden" name="decision" id="appr-decision">
      <div class="modal-header">
        <h5 class="modal-title" id="decisionTitle">{{ __('تأكيد الإجراء') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('إغلاق') }}"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">{{ __('ملاحظات') }}</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('اكتب ملاحظاتك (اختياري)') }}"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
        <button class="btn btn-primary">{{ __('تأكيد') }}</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  // فتح المودال مع تمرير المستوى والقرار
  document.querySelectorAll('.js-open-decision').forEach(btn=>{
    btn.addEventListener('click', function(){
      const level = this.getAttribute('data-level');
      const dec   = this.getAttribute('data-decision');
      document.getElementById('appr-level').value    = level;
      document.getElementById('appr-decision').value = dec;
      document.getElementById('decisionTitle').innerText =
        dec === 'approved' ? '{{ __("تأكيد الاعتماد") }}' : '{{ __("تأكيد الرفض") }}';
      new bootstrap.Modal(document.getElementById('decisionModal')).show();
    });
  });

  // إيقاف تكرار النقر وإخفاء الأزرار بعد الإرسال
  document.querySelectorAll('.js-disable-on-submit').forEach(function(f){
    f.addEventListener('submit', function(){
      f.querySelectorAll('button').forEach(b=>b.disabled=true);
      const level = document.getElementById('appr-level')?.value;
      if (level){
        // إخفاء أزرار الاعتماد/الرفض للصف الحالي (UI فقط؛ الحالة ستتحدث بعد الرجوع)
        document.querySelectorAll(`[data-level="${level}"].js-open-decision`).forEach(b=>b.classList.add('d-none'));
      }
    });
  });
})();
</script>
@endsection
