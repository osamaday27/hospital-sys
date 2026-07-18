@extends('layouts.master')

@section('title')
    {{ trans('lockers_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('lockers_trans.title') }}</a>
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
            <div class="col-lg-12">
                @include('partials.submain', [
                    'title' => __('lockers_trans.lockers_programs'),
                    'cards' => [
                        [
                            'icon' => 'bsdkzyjd.json',
                            'text' => __('lockers_trans.locker_desktop'),
                            'link' => 'locker_desktop',
                        ],
                        [
                            'icon' => 'zruuduya.json',
                            'text' => __('lockers_trans.requests_cancel_receipts'),
                            'link' => 'requests_cancel_receipts',
                        ],
                        [
                            'icon' => 'bktacmnd.json',
                            'text' => __('lockers_trans.discount_requests'),
                            'link' => 'discount_requests',
                        ],
                         [
                            'icon' => 'olfvnikl.json',
                            'text' => __('lockers_trans.delivering_sub_lockers_main_lockers'),
                            'link' => 'delivering_sub_lockers_main_lockers',
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
