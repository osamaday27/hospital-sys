@extends('layouts.master')

@section('title', $title ?? 'طباعة عرض السعر')

@section('content')
    @include('components.print.price_quotations', [
        'title' => $title ?? 'طباعة عرض السعر',
        'movement_summary' => $movement_summary,
        'movement_items' => $movement_items
    ])
@endsection
