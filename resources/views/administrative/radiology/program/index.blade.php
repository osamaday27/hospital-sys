@extends('layouts.master')

@section('title')
    {{ trans('radiology.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('radiology.title') }}</a>
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

            <!-- Radiology Management Section -->
            <div class="col-lg-12">

                @include('partials.icon', [
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-ray-start',
                            'text' => __('radiology_trans.radiology_reception'),
                            'link' => 'radiology/reception',
                        ],
                        [
                            'icon' => 'mdi mdi-file-find',
                            'text' => __('radiology_trans.radiology_orders_management'),
                            'link' => 'radiology/orders',
                        ],
                    ],
                ])

            </div>

        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->

@endsection
