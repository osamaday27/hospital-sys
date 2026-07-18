@extends('layouts.masterliveware')

@section('title')
    {{ trans('suppliers.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">{{ __('suppliers.home') }}</a></li>
                                <li class="breadcrumb-item"><a href="suppliers_program">{{ __('suppliers.programs') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('suppliers.store_invoices') }}</li>
                            </ol>
                        </nav>
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
                @livewire('suppliers.program.edit-store-invoice', ['id' => $id])
            </div>
        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
    </div>
@endsection
