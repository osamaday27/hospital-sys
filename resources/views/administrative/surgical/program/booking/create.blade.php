@extends('layouts.masterliveware')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

<style>
    .fixed-bottom {
        position: fixed !important;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    }

    /* ارفع المودال فوق أي عناصر fixed */
    .modal {
        z-index: 1080;
    }

    .modal-backdrop {
        z-index: 1075;
    }

    /* ضمان عدم ظهور شريط أفقي مع المودال */
    body.modal-open {
        overflow: hidden;
        padding-right: 0 !important;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('surgical_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('surgical_booking_list.index') }}">{{ trans('surgical_trans.surgical_booking') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('surgical_trans.create_booking') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- زر الذهاب لإنشاء مريض --}}
        <div class="mb-3 text-end">
            <a href="{{ route('medical_record_add_s.create') }}" class="btn btn-outline-primary">
           {{ trans('patient_trans.patient_new') }}
            </a>
        </div>


        @livewire('surgery.create-booking', [])

    </div>
@endsection
