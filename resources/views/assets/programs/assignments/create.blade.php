@extends('layouts.master_table')
@section('title', __('assets.create'))
@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('assets.programs.assignments.index') }}">{{ __('assets.asstitle') }}</a></li>
          <li class="breadcrumb-item active">{{ __('assets.create') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@includeWhen($errors->any(),'partials.alert-errors')
@include('partials.alert-success')

<div class="card"><div class="card-body">
  <form method="post" action="{{ route('assets.programs.assignments.store') }}" enctype="multipart/form-data">
    @include('assets.programs.assignments._form', ['row' => null, 'currentText' => null])
    <div class="mt-3 text-end d-flex gap-2 justify-content-end">
      <button class="btn btn-primary" name="submit_action" value="stay">{{ __('assets.save_stay') }}</button>
      <button class="btn btn-success" name="submit_action" value="back">{{ __('assets.save_back') }}</button>
    </div>
  </form>
</div></div>
@endsection
