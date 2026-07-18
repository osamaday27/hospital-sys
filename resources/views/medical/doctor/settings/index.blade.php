@extends('layouts.master')

@section('title')
    {{ trans('doctor_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('doctor_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Section -->
    <div class="row">
        <div class="row justify-content-evenly mt-3">

            <div class="col-lg-12">
                @include('partials.icon', [
                    'title' => __('doctor_trans.doctor_settings'),
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-clipboard-text-outline', 
                            'text' => __('doctor_trans.complaining'), 
                            'link' => 'setting_complaining',
                        ],
                        [
                            'icon' => 'mdi mdi-alert-decagram-outline', 
                            'text' => __('doctor_trans.Allergies'), 
                            'link' => 'setting_allergies',
                        ],
                        [
                            'icon' => 'mdi mdi-eye-check-outline', 
                            'text' => __('doctor_trans.examination_d'), 
                            'link' => 'setting_examination',
                        ],
                        [
                            'icon' => 'mdi mdi-history', 
                            'text' => __('doctor_trans.medical_history'), 
                            'link' => 'setting_medical_history',
                        ],
                        [
                            'icon' => 'mdi mdi-file-document-edit-outline', 
                            'text' => __('doctor_trans.setting_repport'), 
                            'link' => 'setting_repport',
                        ],
                        [
                            'icon' => 'mdi mdi-file-tree-outline', 
                            'text' => __('doctor_trans.medical_sheets'), 
                            'link' => 'setting_medical_sheets',
                        ],
                    ],
                ])
            </div>

        </div>
    </div>
@endsection
