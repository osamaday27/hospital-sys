@php
  /** @var \App\models\assets\assetdocument|null $row */
  $row = $row ?? null;
  $selAsset    = old('asset_id',    $row->asset_id    ?? '');
  $selSupplier = old('supplier_id', $row->supplier_id ?? '');
@endphp

@csrf

{{-- لتحسين التجربة في البحث داخل القوائم --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

<div class="row g-3">

  {{-- الأصل --}}
  <div class="col-md-6">
    <label class="form-label">@lang('assets.asset')</label>
    <select name="asset_id" id="asset_id" class="form-select sel2" required>
      <option value="">@lang('assets.choose')</option>

      {{-- لو الأصل الحالي غير موجود بالقائمة لأي سبب، أضِفه كخيار أول --}}
      @if($row && $row->asset && !$assets->contains('id', $row->asset->id))
        <option value="{{ $row->asset->id }}" selected>
          {{ $row->asset->code }} — {{ $row->asset->name_ar ?? $row->asset->name_en ?? '' }}
        </option>
      @endif

      @foreach($assets as $a)
        <option value="{{ $a->id }}"
          @selected($selAsset == $a->id)>
          {{ $a->code }} — {{ $a->name_ar ?? $a->name_en ?? '' }}
        </option>
      @endforeach
    </select>
    @error('asset_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- نوع الوثيقة --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.type')</label>
    <select name="doc_type" class="form-select" required>
      @php $selType = old('doc_type', $row->doc_type ?? 'invoice'); @endphp
      <option value="invoice"  @selected($selType==='invoice') >@lang('assets.doc_invoice')</option>
      <option value="warranty" @selected($selType==='warranty')>@lang('assets.doc_warranty')</option>
      <option value="other"    @selected($selType==='other')   >@lang('assets.doc_other')</option>
    </select>
    @error('doc_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- رقم الوثيقة --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.doc_no')</label>
    <input type="text" name="doc_no" value="{{ old('doc_no', $row->doc_no ?? '') }}" class="form-control">
    @error('doc_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- التاريخ --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.doc_date')</label>
    <input type="date" name="doc_date" value="{{ old('doc_date', optional($row->doc_date ?? null)->toDateString()) }}" class="form-control">
    @error('doc_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- المورد --}}
  <div class="col-md-6">
    <label class="form-label">@lang('assets.supplier')</label>
    <select name="supplier_id" id="supplier_id" class="form-select sel2">
      <option value="">@lang('assets.choose_optional')</option>

      {{-- لو المورد الحالي غير موجود بالقائمة، أضِفه كخيار لضمان ظهوره محددًا --}}
      @php $hasCurrentSupplier = $row && $row->supplier ? $suppliers->contains('id', $row->supplier->id) : true; @endphp
      @if($row && $row->supplier && !$hasCurrentSupplier)
        <option value="{{ $row->supplier->id }}" selected>
          {{ $row->supplier->name_ar ?? $row->supplier->name_en ?? $row->supplier->code }}
        </option>
      @endif

      @foreach($suppliers as $s)
        <option value="{{ $s->id }}"
          @selected($selSupplier == $s->id)>
          {{ $s->name_ar ?? $s->name_en ?? $s->code }}
        </option>
      @endforeach
    </select>
    @error('supplier_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- التكلفة --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.cost')</label>
    <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $row->cost ?? '') }}" class="form-control">
    @error('cost')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الملف --}}
  <div class="col-md-9">
    <label class="form-label">@lang('assets.file')</label>
    <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    @error('file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

    @if(!empty($row?->file_path))
      @php $fileUrl = asset(ltrim($row->file_path,'/')); @endphp
      <div class="mt-2">
        <a href="{{ $fileUrl }}" target="_blank">@lang('assets.download_current_file')</a>
      </div>
    @endif
  </div>

  {{-- ملاحظات --}}
  <div class="col-12">
    <label class="form-label">@lang('assets.notes')</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $row->notes ?? '') }}</textarea>
    @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

</div>

<script>
  $(function(){
    $('.sel2').select2({ width:'100%' });

    // احتياطي: ضبط القيمة المختارة صراحة (لو تم توليد العناصر ديناميكيًا)
    const selAsset    = @json($selAsset);
    const selSupplier = @json($selSupplier);
    if (selAsset)    $('#asset_id').val(String(selAsset)).trigger('change');
    if (selSupplier) $('#supplier_id').val(String(selSupplier)).trigger('change');
  });
</script>
