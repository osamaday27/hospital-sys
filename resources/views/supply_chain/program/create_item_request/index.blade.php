@extends('layouts.master_doctor')

@section('title')
  {{ __('supply_chain_trans.item_requests') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Breadcrumb --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('create_item_request.index') }}">{{ __('supply_chain_trans.item_requests') }}</a>
          </li>
          <li class="breadcrumb-item active">{{ __('supply_chain_trans.item_requests') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row mb-3">
  <div class="col d-flex justify-content-between align-items-center">
    <h4>{{ __('supply_chain_trans.item_requests') }}</h4>
    <a href="{{ route('create_item_request.create') }}" class="btn btn-primary">
      <i class="ri-add-line"></i> {{ __('supply_chain_trans.create_item_request') }}
    </a>
  </div>
</div>

{{-- Success Alert --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card">
  <div class="card-body p-0">
    <table id="requests-table" class="table table-bordered dt-responsive nowrap align-middle w-100 text-center">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>{{ __('supply_chain_trans.request_number') }}</th>
          <th>{{ __('supply_chain_trans.request_date') }}</th>
          <th>{{ __('supply_chain_trans.user') }}</th>
          <th>{{ __('general.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($requests as $req)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $req->request_number }}</td>
            <td>{{ $req->request_date->format('Y-m-d') }}</td>
            <td>{{ $req->user->name }}</td>
            <td>
              <a href="{{ route('create_item_request.edit', $req) }}" class="btn btn-sm btn-info">
                <i class="ri-eye-line"></i> {{ __('general.view') }}
              </a>
              <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $req->id }}">
                <i class="ri-delete-bin-line"></i> {{ __('general.delete') }}
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@include('components.modals.delete-modal')

<script>
$(function() {
  // إعداد الـ CSRF
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // إخفاء أي alert-dismissible بعد 3 ثوانٍ
  $('.alert-dismissible').each(function() {
    let $alert = $(this);
    setTimeout(function() {
      $alert.fadeOut(500, function() { $alert.remove(); });
    }, 3000);
  });

  // تهيئة الجدول
  var table = $('#requests-table').DataTable({
    responsive: true,
    order: [[1, 'desc']]
  });

  // فتح المودال عند الضغط على زر الحذف
  $(document).on('click', '.delete-btn', function() {
    $('#delete-id').val($(this).data('id'));
    $('#deleteModal').modal('show');
  });

  // تأكيد الحذف
  $(document).on('click', '#delete-record', function() {
    var id  = $('#delete-id').val();
    var url = '{{ route("create_item_request.destroy", ":id") }}'.replace(':id', id);

    $.ajax({
      url: url,
      type: 'DELETE',
      success: function(resp) {
        $('#deleteModal').modal('hide');

        // عرض رسالة نجاح مؤقتة
        var $alert = $(
          `<div class="alert alert-success alert-dismissible fade show text-center" role="alert"
                style="position:fixed; top:20px; left:50%; transform:translateX(-50%); z-index:9999;">
             ${resp.message || '{{ __("تم حذف الطلب بنجاح") }}'}
             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
           </div>`
        ).appendTo('body');

        // إخفائها بعد 3 ثوانٍ
        setTimeout(function() {
          $alert.fadeOut(500, function() { $alert.remove(); });
        }, 3000);

        // إزالة الصف من الجدول دون إعادة تحميل الصفحة
        table
          .row($(`button.delete-btn[data-id="${id}"]`).parents('tr'))
          .remove()
          .draw(false);
      },
      error: function(xhr) {
        var msg = '{{ __("حدث خطأ أثناء الحذف") }}';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        alert(msg);
      }
    });
  });
});
</script>

@endsection
