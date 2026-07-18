@extends('layouts.master')

@section('content')
    @include('components.print.invoice_payment_print', [
        'title' => 'فاتورة دفع',
        'receipt' => $receipt,
    ])
@endsection
