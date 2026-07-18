@extends('layouts.master')

@section('title')
    {{ trans('outpatient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('outpatient_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav nav-justified gap-2 mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light active" data-bs-toggle="tab"
                                href="#animation-new_reservations" role="tab">
                                <i class="ri-calendar-check-line  fs-20 mb-1  align-middle me-1"></i>
                                {{ trans('outpatient_trans.new_reservations') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light" data-bs-toggle="tab" href="#animation-reservations"
                                role="tab">
                                <i class="ri-calendar-todo-fill  fs-20 mb-1  align-middle me-1"></i>
                                {{ trans('outpatient_trans.reservations') }}

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light" data-bs-toggle="tab"
                                href="#animation-future_reservations" role="tab">
                                <i class="ri-calendar-todo-line  fs-20 mb-1  align-middle me-1"></i>
                                {{ trans('outpatient_trans.future_reservations') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light" data-bs-toggle="tab" href="#animation-inquiries"
                                role="tab">
                                <i class="ri-file-search-line  fs-20 mb-1  align-middle me-1"></i>
                                {{ trans('outpatient_trans.inquiries') }}

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light" data-bs-toggle="tab" href="#animation-safe"
                                role="tab">
                                <i class="ri-safe-2-line  fs-20 mb-1  align-middle me-1"></i>
                                {{ trans('outpatient_trans.safe') }}

                            </a>
                        </li>
                    </ul>
                    <div class="tab-content text-muted">

                        <div class="tab-pane active" id="animation-new_reservations" role="tabpanel">
                            @include('administrative.outpatient.components.program.new_reservations')
                        </div>


                        <div class="tab-pane" id="animation-reservations" role="tabpanel">


                        </div>

                        <div class="tab-pane" id="animation-future_reservations" role="tabpanel">

                        </div>

                        <div class="tab-pane" id="animation-inquiries" role="tabpanel">

                        </div>

                        <div class="tab-pane" id="animation-safe" role="tabpanel">

                        </div>

                    </div>
                </div><!-- end card-body -->
            </div>
        </div>
        <!--end col-->

    </div>


@endsection
