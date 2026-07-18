@extends('layouts.master')

@section('title')
    {{ trans('glaccounts_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('glaccounts_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <!-- Content Row -->
        <div class="row justify-content-evenly mt-3">

            <!-- Service Management Section -->
             <!-- Service Management Section -->
             <div class="col-lg-12">
                @include('partials.icon', [
                    'title' => __('glaccounts_trans.glaccounts_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-family-tree',
                            'text' => __('glaccounts_trans.glaccounts'),
                            'link' => 'glaccounts',
                        ],
                        [
                            'icon' => 'mdi mdi-clipboard-check-outline',
                            'text' => __('glaccounts_trans.receiving_inspecting_items'),
                            'link' => 'receiving_inspecting_items',
                        ],
                        
                       
                    ],
                ])
            </div>
        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
    </div>
@endsection
