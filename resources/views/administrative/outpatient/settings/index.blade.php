@extends('layouts.master_table')

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
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body">
                   
                    <div class="tab-content text-muted">

                        <div class="tab-pane active" id="animation-clinic_settings" role="tabpanel">
                            @include('administrative.outpatient.components.settings.index')
                        </div>

                    </div>
                </div><!-- end card-body -->
            </div>
        </div>
        <!--end col-->

    </div>


@endsection
