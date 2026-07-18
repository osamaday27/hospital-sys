{{-- resources/views/assets/settings/brands/edit.blade.php --}}
@extends('layouts.master_table')
@section('title', __('assets.edit'))
@section('content')

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('assets.settings.brands.index') }}">@lang('assets.brands')</a>
          </li>
          <li class="breadcrumb-item active">@lang('assets.edit')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">

    {{-- رسائل النجاح --}}
    @if(session('success'))
      <div class="alert alert-success text-center"
           style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
        {{ session('success') }}
      </div>
      <script>
        $(function(){ setTimeout(function(){ $('.alert-success').fadeOut(600,function(){ $(this).remove(); }); }, 3000); });
      </script>
    @endif

    {{-- رسائل الأخطاء --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="post" action="{{ route('assets.settings.brands.update',$x->id) }}">
      @csrf
      @method('PUT')

      @include('assets.settings.brands._form',['row'=>$x])

      {{-- أزرار الحفظ المزدوجة --}}
      <div class="mt-3 text-end d-flex gap-2 justify-content-end">
        <button class="btn btn-primary" name="submit_action" value="stay">
          @lang('assets.save') &nbsp;–&nbsp; @lang('assets.stay_here')
        </button>
        <button class="btn btn-success" name="submit_action" value="back">
          @lang('assets.save') &nbsp;–&nbsp; @lang('assets.back_to_list')
        </button>
      </div>
    </form>

  </div>
</div>
@endsection
