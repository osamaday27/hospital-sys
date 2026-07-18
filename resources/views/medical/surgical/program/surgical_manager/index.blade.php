@extends('layouts.masterliveware')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
           @livewire('surgical.surgicalmanager', [ ])
        </div>
    </div>
  

@endsection
