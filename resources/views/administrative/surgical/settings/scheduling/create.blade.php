@extends('layouts.masterliveware')
@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    @livewire('administrative.surgical.schedule-room', ['encryptedId' => $id])
@endsection