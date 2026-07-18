{{-- resources/views/doctor/index.blade.php (مثال) --}}
@extends('layouts.master')

@section('title')
    {{ trans('doctor_trans.title') }}
@stop

@push('styles')
    <style>
        /* إطار عام */
        .page-wrap {
            padding-block: 10px 18px;
        }

        .block {
            border-radius: 20px;
            background: #fff;
            border: 1px solid rgba(13, 110, 253, .06);
            box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
            padding: 14px;
            margin-bottom: 16px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: 12px
        }

        .section-head .title {
            font-weight: 800;
            color: #0d6efd;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: .5rem
        }

        .section-head .title .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0d6efd;
            opacity: .2
        }

        /* Dark */
        [data-bs-theme="dark"] .block {
            background: linear-gradient(180deg, rgba(20, 24, 28, .92), rgba(20, 24, 28, .85));
            border-color: rgba(255, 255, 255, .06);
            box-shadow: 0 10px 26px rgba(0, 0, 0, .35);
        }
    </style>
@endpush

@section('content')
    <div class="page-wrap">
        {{-- العنوان وخبز التتبّع --}}
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

        {{-- إدارة الأطباء (الأقسام الرئيسية) --}}
        <div class="block">
            @include('partials.icon', [
                'cards' => [
                    [
                        'icon' => 'mdi mdi-hospital-building',
                        'text' => __('doctor_trans.outpatient_clinics'),
                        'link' => 'outpatient_clinics',
                    ],
                    [
                        'icon' => 'mdi mdi-bed-outline',
                        'text' => __('doctor_trans.inpatient'),
                        'link' => 'inpatient_doctor',
                    ],
            
                    [
                        'icon' => 'mdi mdi-tooth',
                        'text' => __('doctor_trans.dentistry'),
                        'link' => 'dentistry',
                    ],
                    [
                        'icon' => 'mdi mdi-face-woman',
                        'text' => __('doctor_trans.dermatology'),
                        'link' => 'dermatology',
                    ],
                ],
                'title' => __('doctor_trans.title'),
            ])
        </div>

        {{-- برنامج الأطباء - عيون --}}
        <div class="block">
            <div class="section-head">
                <div class="title"><span class="dot"></span><span>{{ __('doctor_trans.doctor_program') }}</span></div>
            </div>
            @include('partials.icon', [
                'cards' => [
                    [
                        'icon' => 'mdi mdi-eye',
                        'text' => __('doctor_trans.ophthalmology_examination'),
                        'link' => 'ophthalmology_examination',
                    ],
                    [
                        'icon' => 'mdi mdi-eye',
                        'text' => __('doctor_trans.ophthalmology'),
                        'link' => 'ophthalmology',
                    ],
                ],
            ])
        </div>
    </div>
@endsection
