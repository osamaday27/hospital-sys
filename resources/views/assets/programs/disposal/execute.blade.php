@extends('layouts.master_table')
@section('title') {{ __('assets.disposal.execute_title') }} @stop
@section('content')
@include('assets.programs.disposal.flash')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('assets.programs.disposal.index') }}">{{ __('assets.disposal.index_title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.programs.disposal.show',$row->id) }}">{{ $row->request_no }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('assets.disposal.execute_title') }}</li>
  </ol>
</nav>

<form method="post" action="{{ route('assets.programs.disposal.execute',$row->id) }}" class="card">
  @csrf
  <div class="card-body">
    @if($row->method==='sale')
      <div class="row g-3 mb-3">
        <div class="col-md-3"><input class="form-control" name="buyer_name" placeholder="{{ __('اسم المشتري') }}" required></div>
        <div class="col-md-3"><input class="form-control" name="buyer_id_number" placeholder="{{ __('رقم هوية (اختياري)') }}"></div>
        <div class="col-md-3"><input class="form-control" name="sale_invoice_no" placeholder="{{ __('رقم الفاتورة') }}" required></div>
        <div class="col-md-3"><input class="form-control" name="sale_amount" type="number" step="0.01" placeholder="{{ __('قيمة البيع') }}" required></div>
        <div class="col-md-3"><input class="form-control" name="vat_amount" type="number" step="0.01" placeholder="{{ __('ضريبة') }}"></div>
        <div class="col-md-3"><input class="form-control" name="fees_amount" type="number" step="0.01" placeholder="{{ __('رسوم') }}"></div>
      </div>
    @elseif($row->method==='donation')
      <div class="row g-3 mb-3">
        <div class="col-md-4"><input class="form-control" name="donee_name" placeholder="{{ __('الجهة المتبرع لها') }}" required></div>
        <div class="col-md-4"><input class="form-control" name="donation_doc_no" placeholder="{{ __('رقم المستند') }}"></div>
      </div>
    @else
      <div class="row g-3 mb-3">
        <div class="col-md-4"><input class="form-control" name="destruction_method" placeholder="{{ __('طريقة الإعدام/الشطب') }}" required></div>
        <div class="col-md-4"><input class="form-control" name="destruction_doc_no" placeholder="{{ __('رقم المستند') }}"></div>
        <div class="col-md-4"><input class="form-control" name="witnessed_by" placeholder="{{ __('بحضور') }}"></div>
      </div>
    @endif

    <table class="table table-bordered">
      <thead><tr>
        <th>#</th><th>{{ __('assets.asset') }}</th><th>{{ __('assets.qty_requested') }}</th><th>{{ __('assets.qty_execute') }}</th>
      </tr></thead>
      <tbody>
        @foreach($row->items as $i=>$it)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $it->asset?->code }} — {{ $it->asset?->name_ar ?? $it->asset?->name_en }}</td>
            <td>{{ $it->qty_requested }}</td>
            <td>
              <input type="hidden" name="items[{{ $i }}][request_item_id]" value="{{ $it->id }}">
              <input type="number" name="items[{{ $i }}][qty_executed]" class="form-control" min="0" max="{{ $it->qty_requested }}" value="{{ $it->qty_requested }}">
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer d-flex justify-content-end gap-2">
    <a href="{{ route('assets.programs.disposal.show',$row->id) }}" class="btn btn-light">{{ __('assets.back') }}</a>
    <button class="btn btn-success">{{ __('assets.disposal.execute') }}</button>
  </div>
</form>
@endsection
