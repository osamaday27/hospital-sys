@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}



@stop


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('settings_trans.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('settings_trans.settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('settings_trans.scheduling') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('settings_trans.scheduling_settings') }}</h5>
                    {{-- Message --}}
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                data-dismiss="alert">
                                <i class="fa fa-times"></i>
                            </button>
                            <strong>Success !</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                data-dismiss="alert">
                                <i class="fa fa-times"></i>
                            </button>
                            <strong>Error !</strong> {{ session('error') }}
                        </div>
                    @endif

                </div>


                <!-- Buttons Grid -->
                <div class="d-grid gap-2">
                    <a class="btn btn-primary waves-effect waves-light" href="{{ route('scheduling_settings.create') }}">
                        {{ trans('settings_trans.add_scheduling') }}
                    </a>
                </div>


                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false">{{ trans('settings_trans.sr_no') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.name') }}</th>
                                <th>{{ trans('settings_trans.specialty') }}</th>
                                <th>{{ trans('settings_trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->special->name }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item edit-item-btn"
                                                        href="{{ route('scheduling_settings.edit', ['id' => $data->id]) }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        {{ trans('settings_trans.edit') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/pages/form-pickers.init.js') }}"></script>

@endsection
