@extends('layouts.master')

@section('content')
    @include('components.print.printopening_balance', [
        'title' => 'رصيد افتتاحي',
        'movement_summary' => $movement_summary,
        'movement_items' => $movement_items
    ])
@endsection
