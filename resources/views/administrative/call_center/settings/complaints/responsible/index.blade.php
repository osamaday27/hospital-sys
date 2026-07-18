@extends('layouts.master_table')

@section('title')
    {{ trans('call_center_trans.responsible') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- حاوية الرسائل -->
    <div id="alertContainer">
        @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show text-center"
         style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: auto; min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">
        {{ session('success') }}

    </div>
@endif
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('complaints-category.index') }}">{{ trans('call_center_trans.complaints') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.responsible') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ trans('call_center_trans.responsibles') }}</h4>
                    <a href="{{ route('complaints-category.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> {{ trans('general.back') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('complaints-responsible.create') }}" method="POST">
                        @csrf
                        @method("HEAD")
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox1" name="sectionManager" value="1"
                                    {{ $sectionManagerStatus == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox1">{{trans('call_center_trans.section_manager')}}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox2" name="departmentManager" value="1"
                                    {{ $departmentManagerStatus == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox2">{{trans('call_center_trans.department_manager')}}</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> {{ trans('call_center_trans.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
    setTimeout(function(){
        $("#alertContainer .alert").fadeOut("slow", function(){
            $(this).remove();
        });
    }, 3000);
});

    </script>
@endsection
