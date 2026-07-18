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

    <!-- الأقسام -->
    <div class="row justify-content-evenly mt-3">

        <!-- إعدادات الخزائن -->
        <div class="col-lg-12">
            @include('partials.icon', [
                'title' => __('lockers_trans.locker_settings'),
                'cards' => [
                    [
                        'icon' => 'mdi mdi-safe',
                        'text' => __('lockers_trans.locker_s'),
                        'link' => 'locker_c_settings',
                    ],
                    [
                        'icon' => 'mdi mdi-bank',
                        'text' => __('lockers_trans.bank_s'),
                        'link' => 'bank-settings',
                    ],
                    [
                        'icon' => 'mdi mdi-credit-card-outline',
                        'text' =>__('lockers_trans.atm_machines'),
                        'link' => 'bank-machines',
                    ],
                    [
                        'icon' => 'mdi mdi-cancel',
                        'text' => __('lockers_trans.reasons_cancellation'),
                        'link' => 'cancellationreason',
                    ],
                    [
                        'icon' => 'mdi mdi-cash-minus',
                        'text' => __('lockers_trans.Reasons_partial_payment'),
                        'link' => 'reasonspartialpayment',
                    ],
                ],
            ])
        </div>

    </div>
    <!-- End col -->
@endsection
