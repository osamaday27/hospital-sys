@extends('layouts.masterliveware')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
           @livewire('surgery.edit-booking', [
                'id' => $encryptedBookingId,])
        </div>
    </div>
@endsection
