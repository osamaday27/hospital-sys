@php
    $fileUrl = $row->file_path ? asset( ltrim($row->file_path, '/')) : null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <div class="border p-2 rounded mb-2">
            <div class="small text-muted">@lang('assets.asset')</div>
            <div class="fw-semibold">
                {{ $row->asset ? ($row->asset->code . ' — ' . ($row->asset->name_ar ?? $row->asset->name_en)) : '-' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-2 rounded mb-2">
            <div class="small text-muted">@lang('assets.type')</div>
            <div class="fw-semibold">{{ __('assets.doc_' . $row->doc_type) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-2 rounded mb-2">
            <div class="small text-muted">@lang('assets.doc_no')</div>
            <div class="fw-semibold">{{ $row->doc_no ?? '-' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-2 rounded mb-2">
            <div class="small text-muted">@lang('assets.doc_date')</div>
            <div class="fw-semibold">{{ $row->doc_date ? $row->doc_date->toDateString() : '-' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-2 rounded mb-2">
            <div class="small text-muted">@lang('assets.supplier')</div>
            <div class="fw-semibold">
                {{ $row->supplier ? (($row->supplier->code ? $row->supplier->code . ' — ' : '') . ($row->supplier->name_ar ?? $row->supplier->name_en ?? '—')) : '—' }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border p-2 rounded mb-2">
            <div class="small text-muted">@lang('assets.cost')</div>
            <div class="fw-semibold">{{ $row->cost ?? '-' }}</div>
        </div>
    </div>
<div class="col-12">
  <div class="border p-2 rounded">
    <div class="small text-muted">@lang('assets.file')</div>
    <div class="fw-semibold">
      @if($fileUrl)
        <a href="{{ $fileUrl }}" target="_blank">@lang('assets.download')</a>
      @else
        -
      @endif
    </div>
  </div>
</div>
    <div class="col-12">
        <div class="border p-2 rounded">
            <div class="small text-muted">@lang('assets.notes')</div>
            <div class="fw-semibold">{{ $row->notes ?? '-' }}</div>
        </div>
    </div>
</div>
