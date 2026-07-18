@extends('layouts.master')

@section('content')
    @include('components.print.movement-template', [
        'title' => 'اضافة أصناف للمخازن',
        'movement_summary' => $movement_summary,
        'movement_items' => $movement_items
    ])
@endsection
