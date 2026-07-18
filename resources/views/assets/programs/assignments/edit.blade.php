@extends('layouts.master_table')
@section('title', __('assets.edit'))
@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('assets.programs.assignments.index') }}">{{ __('assets.asstitle') }}</a></li>
          <li class="breadcrumb-item active">{{ __('assets.edit') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@includeWhen($errors->any(),'partials.alert-errors')
@include('partials.alert-success')

<div class="card"><div class="card-body">
  <form method="post" action="{{ route('assets.programs.assignments.update', $row->id) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('assets.programs.assignments._form')
    <div class="mt-3 text-end d-flex gap-2 justify-content-end">
      <button class="btn btn-primary" name="submit_action" value="stay">{{ __('assets.update_stay') }}</button>
      <button class="btn btn-success" name="submit_action" value="back">{{ __('assets.update_back') }}</button>
    </div>
  </form>
</div></div>
@endsection
