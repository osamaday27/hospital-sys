@extends('layouts.master')

@section('title')
    {{ trans('glaccounts_trans.title') }}
@stop

@section('content')
<div class="container-fluid py-3" dir="rtl">

    {{-- رسائل التنبيه --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    
    <!-- قسم الإعدادات -->
    <div class="row">
        <div class="col-12">
            @include('partials.icon', [
                'title' => __('glaccounts_trans.glaccounts_settings'),
                'cards' => [
                    [
                        'icon' => 'mdi mdi-family-tree',
                        'text' => __('glaccounts_trans.glaccounts'),
                        'link' => 'glaccounts',
                    ],
                 
                ],
            ])
        </div>
    </div>
    <!-- End Content Row -->

</div>
@endsection
