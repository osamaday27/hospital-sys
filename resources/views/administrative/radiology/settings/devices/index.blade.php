@extends('layouts.masterliveware')

@section('title', __('radiology.add_device'))

@section('content')
    @livewire('radiology.settings.devices.index')
@endsection
