@extends('layouts.masterliveware')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
           @livewire('surgical.surgical-booking', [ ])
        </div>
    </div>
  

@endsection
