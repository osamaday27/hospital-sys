
@extends('layouts.master')

@section('title', $title ?? 'طباعة طلب الشراء')

@section('content')
    @include('components.print.purchase_requisitions', [
        'title' => $title ?? 'طباعة طلب الشراء',
        'movement_summary' => $movement_summary,
        'movement_items' => $movement_items
    ])
@endsection
