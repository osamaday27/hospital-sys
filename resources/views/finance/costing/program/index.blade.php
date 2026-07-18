@extends('layouts.master')

@section('title')
    {{ __('costing_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ __('costing_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

        <!-- Operations Section -->
        <div class="row justify-content-evenly mt-4">
            <div class="col-lg-12">
                <h5 class="mb-3 text-success">{{ __('costing_trans.program') }}</h5>
                @include('partials.icon', [
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-play-circle-outline',
                            'text' => __('costing_trans.cost_run'),
                            'link' => 'costing_run_wizard',
                        ],
                        [
                            'icon' => 'mdi mdi-lock-check-outline',
                            'text' => __('costing_trans.batches'),
                            'link' => 'costing_batches',
                        ],
                        [
                            'icon' => 'mdi mdi-compare-horizontal',
                            'text' => __('costing_trans.adjustments'),
                            'link' => 'costing_adjustments',
                        ],
                        [
                            'icon' => 'mdi mdi-upload',
                            'text' => __('costing_trans.post_to_gl'),
                            'link' => 'costing_posting',
                        ],
                    ],
                ])
            </div>
        </div>

       
    </div>
@endsection
