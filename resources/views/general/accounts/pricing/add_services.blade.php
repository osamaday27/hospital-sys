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
                        <li class="breadcrumb-item active">{{ trans('settings_trans.pricing') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.pricing_settings') }}</h5>
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
                <div class="card-body">
                    <form action="{{ route('pricing_services_settings.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <input id="id_pricing" name="id_pricing" value="{{ $pricing->id }}" hidden>
                                    <label for="name_ar" class="form-label">{{ trans('settings_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        placeholder="{{ trans('settings_trans.name_ar_enter') }}"
                                        value="{{ $pricing->getTranslation('name', 'ar') }}" readonly>
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        placeholder="{{ trans('settings_trans.name_en_enter') }}"
                                        value="{{ $pricing->getTranslation('name', 'en') }}" readonly>
                                </div>
                            </div><!--end col-->
                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="default" class="form-label">{{ trans('settings_trans.services') }}</label>
                                    <select name="id_services" id="id_services" class="form-control" required>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.cost_price') }}</label>
                                    <input type="text" class="form-control" id="cost_price" name="cost_price"
                                        placeholder="{{ trans('settings_trans.cost_price_enter') }}" value="">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.patient_price') }}</label>
                                    <input type="text" class="form-control" id="patient_price" name="patient_price"
                                        placeholder="{{ trans('settings_trans.patient_price_enter') }}" value="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit"
                                        class="btn btn-primary">{{ trans('settings_trans.add') }}</button>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </form>


                </div>
                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false">{{ trans('settings_trans.sr_no') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.id') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.services_name') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.cost_price') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.patient_price') }}</th>
                                <th>{{ trans('settings_trans.create_date') }}</th>
                                <th>{{ trans('settings_trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach ($pricing_services_settings as $pricing_services_settings)
                                <tr>
                                    <?php $i++; ?>
                                    <td>{{ $i }}</td>
                                    <td>{{ $pricing_services_settings->id }}</td>
                                    <td>{{ $pricing_services_settings->services->name }}</td>
                                    <td>{{ $pricing_services_settings->cost_price }}</td>
                                    <td>{{ $pricing_services_settings->patient_price }}</td>
                                    <td>{{ $pricing_services_settings->created_at }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><button data-bs-toggle="modal"
                                                        data-bs-target="#signupModal{{ $pricing_services_settings->id }}"
                                                        class="dropdown-item edit-item-btn"> <i
                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                        Delete</button></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!-- edit -->
                                <div id="signupModal{{ $pricing_services_settings->id }}" class="modal fade"
                                    tabindex="-1" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <lord-icon src="{{ URL::asset('assets/images/icon/tdrtiskw.json') }}"
                                                    trigger="loop" colors="primary:#f7b84b,secondary:#405189"
                                                    style="width:130px;height:130px">
                                                </lord-icon>
                                                <div class="mt-4 pt-4">
                                                    <h4>{{ trans('settings_trans.massagedelete_d') }}!</h4>
                                                    <p class="text-muted">{{ trans('settings_trans.massagedelete_p') }}
                                                        {{ $pricing_services_settings->name }}</p>
                                                    <!-- Toogle to second dialog -->
                                                    <form action="{{ route('pricing_services_settings.destroy', 'test') }}"
                                                        method="post">
                                                        {{ method_field('delete') }}
                                                        {{ csrf_field() }}
                                                        <input class="form-control" id="id" name="id"
                                                            value="{{ $pricing_services_settings->id }}" type="hidden">

                                                        <button class="btn btn-warning" data-bs-target="#secondmodal"
                                                            data-bs-toggle="modal" data-bs-dismiss="modal">
                                                            {{ trans('settings_trans.massagedelete_pricing') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
