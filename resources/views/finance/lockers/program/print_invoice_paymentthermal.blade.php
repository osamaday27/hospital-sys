@extends('layouts.master')

@section('content')
    @include('components.print.receipt_thermal', [
        'title' => 'فاتورة دفع',
        'receipt' => $receipt,
    ])
@endsection
