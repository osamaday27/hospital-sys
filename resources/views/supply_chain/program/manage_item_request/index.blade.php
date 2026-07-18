@extends('layouts.master_doctor')

@section('title')
  {{ __('supply_chain_trans.manage_item_requests') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
  // نحدد ما إذا كانت هذه صفحة الطلبات السابقة
  var isPrevious = {{ $previous ? 'true' : 'false' }};
</script>

{{-- Breadcrumb --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            {{ __('supply_chain_trans.manage_item_requests') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- فلترة: عرض نشط أو سابق --}}
<div class="row mb-3">
  <div class="col d-flex justify-content-between align-items-center">
    <h4>{{ __('supply_chain_trans.manage_item_requests') }}</h4>
    <div class="btn-group">
      @if($previous)
        <a href="{{ route('manage_item_request.index') }}" class="btn btn-outline-secondary">
          {{ __('supply_chain_trans.show_active') }}
        </a>
      @else
        <a href="{{ route('manage_item_request.index', ['previous'=>1]) }}" class="btn btn-outline-secondary">
          {{ __('supply_chain_trans.show_previous') }}
        </a>
      @endif
    </div>
  </div>
</div>

<table class="table table-bordered text-center">
  <thead class="table-light">
    <tr>
      <th>#</th>
      <th>{{ __('supply_chain_trans.request_number') }}</th>
      <th>{{ __('supply_chain_trans.request_date') }}</th>
      <th>{{ __('supply_chain_trans.store') }}</th>
      <th>{{ __('supply_chain_trans.user') }}</th>
      <th>{{ __('general.actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach($requests as $req)
      <tr data-id="{{ $req->id }}">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $req->request_number }}</td>
        <td>{{ $req->request_date->format('Y-m-d') }}</td>
        <td>{{ $req->store->name_ar }} – {{ $req->store->name_en }}</td>
        <td>{{ $req->user->name }}</td>
        <td>
          <button class="btn btn-sm btn-info view-btn">
            <i class="ri-eye-line"></i> {{ __('general.view') }}
          </button>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

{{-- مودال عرض تفاصيل الطلب --}}
<div class="modal fade" id="detailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('supply_chain_trans.request_details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered text-center" id="details-table">
          <thead class="table-light" id="details-header"><!-- يبنى عبر JS --></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
  // إعداد CSRF لأجاكس
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // بناء رأس جدول التفاصيل حسب حالة العرض
  function buildDetailsHeader(){
    if (isPrevious) {
      return `
        <tr>
          <th>#</th>
          <th>{{ __('supply_chain_trans.item_name_ar') }}</th>
          <th>{{ __('supply_chain_trans.item_name_en') }}</th>
          <th>{{ __('supply_chain_trans.status') }}</th>
        </tr>`;
    } else {
      return `
        <tr>
          <th>#</th>
          <th>{{ __('supply_chain_trans.item_name_ar') }}</th>
          <th>{{ __('supply_chain_trans.item_name_en') }}</th>
          <th>{{ __('general.actions') }}</th>
        </tr>`;
    }
  }

  // عند الضغط على زر "عرض"
  $(document).on('click', '.view-btn', function(){
    let reqId = $(this).closest('tr').data('id');
    $('#details-header').html(buildDetailsHeader());
    $('#details-table tbody').empty();

    $.getJSON(
      '{{ url("manage_item_request") }}/' + reqId + '/details',
      { all: isPrevious ? 1 : 0 },
      function(data){
        data.forEach(function(d, i){
          if (isPrevious) {
            let badgeClass = d.status === 'approved'
              ? 'bg-success'
              : (d.status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
            let label = d.status === 'approved'
              ? '{{ __("supply_chain_trans.approved") }}'
              : (d.status === 'rejected'
                  ? '{{ __("supply_chain_trans.rejected") }}'
                  : '{{ __("supply_chain_trans.pending") }}');

            $('#details-table tbody').append(`
              <tr>
                <td>${i+1}</td>
                <td>${d.name_ar}</td>
                <td>${d.name_en}</td>
                <td><span class="badge ${badgeClass}">${label}</span></td>
              </tr>`);
          } else {
            $('#details-table tbody').append(`
              <tr data-id="${d.id}">
                <td>${i+1}</td>
                <td>${d.name_ar}</td>
                <td>${d.name_en}</td>
                <td>
                  <button class="btn btn-sm btn-success me-1 approve-btn">
                    {{ __('general.approve') }}
                  </button>
                  <button class="btn btn-sm btn-danger reject-btn">
                    {{ __('general.reject') }}
                  </button>
                </td>
              </tr>`);
          }
        });
        $('#detailsModal').modal('show');
      }
    );
  });

  // إعادة ترتيب أرقام الصفوف داخل المودال
  function updateDetailSerial(){
    $('#details-table tbody tr').each(function(i){
      $(this).find('td:first').text(i+1);
    });
  }

  // معالجة الموافقة/الرفض
  $(document).on('click', '.approve-btn, .reject-btn', function(){
    let $row     = $(this).closest('tr'),
        detailId = $row.data('id'),
        status   = $(this).hasClass('approve-btn') ? 'approved' : 'rejected';

    // بناء URL صحيح لمسار status
    let url = '{{ url("manage_item_request") }}' + '/' + detailId + '/status';

    $.ajax({
      url: url,
      type: 'POST',
      data: { status: status, notes: '' },
      success: function(resp){
        if (resp.success) {
          // نزيل الصف فوراً
          $row.remove();
          updateDetailSerial();
          // إذا انتهت البنود، نغلق المودال ونعيد تحميل الصفحة
          if (!$('#details-table tbody tr').length) {
            $('#detailsModal').modal('hide');
            location.reload();
          }
        }
      },
      error: function(xhr){
        console.error(xhr.responseText);
        alert('حدث خطأ أثناء معالجة الطلب');
      }
    });
  });
});
</script>
@endsection
