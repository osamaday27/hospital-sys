@extends('layouts.masterliveware')

@section('title')
    {{ trans('doctor_trans.medical_sheets') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('doctor_trans.home') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('doctor_trans.doctor_settings') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ __('doctor_trans.design_medical_sheet') }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="row justify-content-evenly mt-3">
        <div class="col-lg-12">
            {{-- ✅ استدعاء لايف واير الخاص بتصميم الشيتات --}}
            @livewire('medical.medical-sheet-template-builder', ['id' => $id])
        </div>
    </div>
@endsection
