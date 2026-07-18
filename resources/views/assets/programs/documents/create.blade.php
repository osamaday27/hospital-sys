@extends('layouts.master_table')
@section('title', __('assets.create'))
@section('content')
@includeWhen($errors->any(),'partials.alert-errors')
@include('partials.alert-success')

<div class="row"><div class="col-12"><div class="page-title-box d-sm-flex align-items-center justify-content-between">
  <div class="page-title-right"><ol class="breadcrumb m-0">
    <li class="breadcrumb-item"><a href="{{ route('assets.programs.documents.index') }}">@lang('assets.documents')</a></li>
    <li class="breadcrumb-item active">@lang('assets.create')</li>
  </ol></div></div></div></div>

<div class="card"><div class="card-body">
  <form method="post" action="{{ route('assets.programs.documents.store') }}" enctype="multipart/form-data">
    @include('assets.programs.documents._form')
    <div class="mt-3 text-end d-flex gap-2 justify-content-end">
      <button class="btn btn-primary" name="submit_action" value="stay">@lang('assets.save') – @lang('assets.stay_here')</button>
      <button class="btn btn-success" name="submit_action" value="back">@lang('assets.save') – @lang('assets.back_to_list')</button>
    </div>
  </form>
</div></div>
@endsection
