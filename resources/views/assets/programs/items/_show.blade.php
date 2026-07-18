{{-- resources/views/assets/programs/items/_show.blade.php --}}
@php
  use Illuminate\Support\Facades\Storage;

  // شارة الحالة
  $status = $row->status ?? 'in_service';
  $cls = match($status){
    'in_service'  => 'badge bg-success',
    'maintenance' => 'badge bg-warning text-dark',
    'retired'     => 'badge bg-secondary',
    'scrapped'    => 'badge bg-danger',
    default       => 'badge bg-light text-dark'
  };

  // دالة تبسيط عرض التاريخ
  $dstr = function($v){ return $v ? optional($v)->toDateString() : '—'; };

  // تحويل المسار المخزَّن إلى رابط عام
  $toPublicUrl = function (?string $path): ?string {
      if (!$path) return null;
      $p = str_replace('\\','/',$path);
      $p = ltrim($p, '/');
      $p = preg_replace('/^public\//i', '', $p);
      return asset($p);
  };
  $qrUrl = $toPublicUrl($row->qr_code_path ?? null);

  // أرقام وملخصات الإهلاك
  $cost      = (float)($row->cost ?? 0);
  $residual  = (float)($row->residual_value ?? 0);
  $accum     = (float)($row->accumulated_depreciation ?? 0);
  $base      = max($cost - $residual, 0);
  $nbv       = (float)($row->net_book_value ?? max($cost - $accum, 0));
  $pct       = $base > 0 ? max(0, min(100, round(($accum / $base) * 100, 2))) : 0;

  // تسميات الطريقة
  $depMethod = $row->dep_method ?: null;
  $depLabel  = $depMethod ? __('assets.dep_method_'.$depMethod) : '—';

  // وحدات UOP (يتم تمرير $uopUsed من الكنترولر؛ إن لم يُمرَّر نجعله 0)
  $uopTotal  = (int)($row->uop_total_estimated_units ?? 0);
  $uopUsed   = isset($uopUsed) ? (int)$uopUsed : 0;
  $uopRemain = $uopTotal > 0 ? max($uopTotal - $uopUsed, 0) : null;
@endphp

<div class="row g-3">

  {{-- معلومات أساسية --}}
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.code')</div>
      <div class="fw-semibold">{{ $row->code }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.name_ar')</div>
      <div class="fw-semibold">{{ $row->name_ar ?: '—' }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.name_en')</div>
      <div class="fw-semibold">{{ $row->name_en ?: '—' }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.serial_no')</div>
      <div class="fw-semibold">{{ $row->serial_no ?: '—' }}</div>
    </div>
  </div>

  {{-- تصنيف/ماركة/موديل/موقع --}}
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.category')</div>
      <div class="fw-semibold">
        {{ $row->category ? ($row->category->name_ar ?? $row->category->name_en ?? '—') : '—' }}
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.brand')</div>
      <div class="fw-semibold">
        {{ $row->brand ? ($row->brand->name ?? $row->brand->code ?? '—') : '—' }}
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.model')</div>
      <div class="fw-semibold">
        {{ $row->model ? ($row->model->name ?? $row->model->code ?? '—') : '—' }}
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.location')</div>
      <div class="fw-semibold">
        {{ $row->location ? ($row->location->name_ar ?? $row->location->name_en ?? '—') : '—' }}
      </div>
    </div>
  </div>

  {{-- مخزن/مورد --}}
  <div class="col-md-4">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.store')</div>
      <div class="fw-semibold">
        @if($row->store)
          {{ $row->store->manual_code ? $row->store->manual_code.' — ' : '' }}
          {{ $row->store->name_ar ?? $row->store->name_en ?? '—' }}
        @else
          —
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.supplier')</div>
      <div class="fw-semibold">
        @if($row->supplier)
          {{ $row->supplier->code ? $row->supplier->code.' — ' : '' }}
          {{ $row->supplier->name_ar ?? $row->supplier->name_en ?? '—' }}
        @else
          —
        @endif
      </div>
    </div>
  </div>

  {{-- الحالة/نشط/RFID --}}
  <div class="col-md-2">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.status')</div>
      <div class="fw-semibold"><span class="{{ $cls }}">{{ __('assets.status_'.$status) }}</span></div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.active')</div>
      <div class="fw-semibold">{{ ($row->is_active ?? false) ? __('assets.yes') : __('assets.no') }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">RFID</div>
      <div class="fw-semibold">{{ $row->rfid_tag ?: '—' }}</div>
    </div>
  </div>

  {{-- تواريخ --}}
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.purchase_date')</div>
      <div class="fw-semibold">{{ $dstr($row->purchase_date) }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.in_service_date')</div>
      <div class="fw-semibold">{{ $dstr($row->in_service_date ?: $row->purchase_date) }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">{{ __('assets.depreciation.last_dep_date') }}</div>
      <div class="fw-semibold">{{ $dstr($row->last_depreciation_date ?? null) }}</div>
    </div>
  </div>

  {{-- أرقام مالية أساسية --}}
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.cost')</div>
      <div class="fw-semibold">{{ number_format($cost, 2) }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.residual_value')</div>
      <div class="fw-semibold">{{ number_format($residual, 2) }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">{{ __('assets.depreciation.depreciable_base') }}</div>
      <div class="fw-semibold">{{ number_format($base, 2) }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.qty')</div>
      <div class="fw-semibold">{{ (int)($row->qty ?? 1) }}</div>
    </div>
  </div>

  {{-- ملخص الإهلاك الحالي --}}
  <div class="col-md-6">
    <div class="border p-3 rounded mb-3">
      <div class="small text-muted mb-1">{{ __('assets.depreciation.summary') }}</div>
      <div class="row">
        <div class="col-6">
          <div class="small text-muted">{{ __('assets.depreciation.accumulated') }}</div>
          <div class="fw-semibold">{{ number_format($accum, 2) }}</div>
        </div>
        <div class="col-6">
          <div class="small text-muted">{{ __('assets.depreciation.nbv') }}</div>
          <div class="fw-semibold">{{ number_format($nbv, 2) }}</div>
        </div>
      </div>
      <div class="mt-2">
        <div class="small text-muted d-flex justify-content-between">
          <span>{{ __('assets.depreciation.progress') }}</span>
          <span>{{ $pct }}%</span>
        </div>
        <div class="progress" style="height:10px;">
          <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- تفاصيل سياسة الإهلاك --}}
  <div class="col-md-6">
    <div class="border p-3 rounded mb-3">
      <div class="small text-muted mb-2">{{ __('assets.depreciation.policy_details') }}</div>

      <div class="row g-2">
        <div class="col-md-4">
          <div class="small text-muted">@lang('assets.dep_method')</div>
          <div class="fw-semibold">{{ $depLabel }}</div>
        </div>

        <div class="col-md-4">
          <div class="small text-muted">@lang('assets.useful_life_months')</div>
          <div class="fw-semibold">{{ $row->dep_method !== 'UOP' ? ($row->useful_life_months ?? '—') : '—' }}</div>
        </div>

        <div class="col-md-4">
          <div class="small text-muted">@lang('assets.dep_rate')</div>
          <div class="fw-semibold">
            {{ $row->dep_method !== 'UOP' ? (isset($row->dep_rate) ? number_format((float)$row->dep_rate, 3).'%' : '—') : '—' }}
          </div>
        </div>

        <div class="col-md-6">
          <div class="small text-muted">@lang('assets.uop_total_estimated_units')</div>
          <div class="fw-semibold">
            {{ $row->dep_method === 'UOP' ? ($uopTotal ?: '—') : '—' }}
          </div>
        </div>

        <div class="col-md-6">
          <div class="small text-muted">{{ __('assets.depreciation.uop_used_remain') }}</div>
          <div class="fw-semibold">
            @if($row->dep_method === 'UOP' && $uopTotal > 0)
              {{ __('assets.depreciation.uop_used_x_of_y', ['used'=>$uopUsed, 'total'=>$uopTotal]) }}
              @if(!is_null($uopRemain))
                — {{ __('assets.depreciation.uop_remaining') }}: {{ $uopRemain }}
              @endif
            @else
              —
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- QR --}}
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3 text-center">
      <div class="small text-muted mb-1">@lang('assets.qr_code')</div>
      @if($qrUrl)
        <img src="{{ $qrUrl }}" alt="QR" style="height:140px;">
      @else
        <span class="text-muted">@lang('assets.not_available')</span>
      @endif
    </div>
  </div>

  {{-- المستندات المرتبطة --}}
  <div class="col-12">
    <div class="border p-3 rounded">
      <div class="small text-muted mb-2">@lang('assets.documents')</div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>@lang('assets.type')</th>
              <th>@lang('assets.doc_no')</th>
              <th>@lang('assets.doc_date')</th>
              <th>@lang('assets.cost')</th>
              <th>@lang('assets.file')</th>
            </tr>
          </thead>
          <tbody>
            @forelse($row->documents as $i => $d)
              @php $docUrl = $toPublicUrl($d->file_path ?? null); @endphp
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ __('assets.doc_'.$d->doc_type) }}</td>
                <td>{{ $d->doc_no ?? '—' }}</td>
                <td>{{ $dstr($d->doc_date) }}</td>
                <td>{{ isset($d->cost) ? number_format($d->cost,2) : '—' }}</td>
                <td>
                  @if($docUrl)
                    <a href="{{ $docUrl }}" target="_blank">@lang('assets.download')</a>
                  @else
                    —
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-muted text-center">@lang('assets.no_content')</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="text-end">
        <a href="{{ route('assets.programs.documents.create', ['asset_id' => $row->id]) }}" class="btn btn-outline-secondary btn-sm">
          <i class="ri-add-line"></i> @lang('assets.add_document')
        </a>
      </div>
    </div>
  </div>

</div>
