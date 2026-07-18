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
                        <li class="breadcrumb-item active">{{ trans('settings_trans.contract') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.contract_settings') }}</h5>
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
                    <form action="{{ route('contract_settings.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <!--end col-->
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-contractel">{{ trans('settings_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        placeholder="{{ trans('settings_trans.name_ar_enter') }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-contractel">{{ trans('settings_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        placeholder="{{ trans('settings_trans.name_en_enter') }}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default" class="form-label">{{ trans('settings_trans.company') }}</label>
                                    <select name="id_company" id="id_company" class="form-control" required>
                                        @foreach ($company as $companyes)
                                            <option value="{{ $companyes->id }}"> {{ $companyes->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default"
                                        class="form-label">{{ trans('settings_trans.price_list') }}</label>
                                    <select name="id_pricing" id="id_pricing" class="form-control" required>
                                        @foreach ($pricing as $pricinges)
                                            <option value="{{ $pricinges->id }}"> {{ $pricinges->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default"
                                        class="form-label">{{ trans('settings_trans.discount_type') }}</label>
                                    <select name="discount_type" id="discount_type" class="form-control" required>
                                        <option value="percent">{{ trans('settings_trans.percent') }}</option>
                                        <option value="value">{{ trans('settings_trans.value') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.discount') }}</label>
                                    <input type="text" class="form-control" id="discount" name="discount"
                                        placeholder="{{ trans('settings_trans.discount_enter') }}" value="">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="default"
                                        class="form-label">{{ trans('settings_trans.patient_contribution_type') }}</label>
                                    <select name="patient_contribution_type" id="patient_contribution_type"
                                        class="form-control" required>
                                        <option value="percent">{{ trans('settings_trans.percent') }}</option>
                                        <option value="value">{{ trans('settings_trans.value') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.patient_contribution') }}</label>
                                    <input type="text" class="form-control" id="patient_contribution"
                                        name="patient_contribution"
                                        placeholder="{{ trans('settings_trans.patient_contribution_enter') }}"
                                        value="">
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
                                <th data-ordering="false">{{ trans('settings_trans.name_ar') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.name_en') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.company') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.pricing') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.discount_type') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.discount') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.patient_contribution_type') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.patient_contribution') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.status') }}</th>
                                <th>{{ trans('settings_trans.create_date') }}</th>
                                <th>{{ trans('settings_trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach ($contract as $contractes)
                                <tr>
                                    <?php $i++; ?>
                                    <td>{{ $i }}</td>
                                    <td>{{ $contractes->id }}</td>
                                    <td>{{ $contractes->getTranslation('name', 'ar') }}</td>
                                    <td>{{ $contractes->getTranslation('name', 'en') }}</td>
                                    <td>{{ $contractes->company->name }}</td>
                                    <td>{{ $contractes->pricing->name }}</td>
                                    <td>{{ $contractes->discount_type }}</td>
                                    <td>{{ $contractes->discount }}</td>
                                    <td>{{ $contractes->patient_contribution_type }}</td>
                                    <td>{{ $contractes->patient_contribution }}</td>
                                    <td>
                                        @if ($contractes->status == 1)
                                            <span class="badge badge-label bg-success"><i
                                                    class="mdi mdi-circle-medium"></i>
                                                {{ trans('settings_trans.default_true') }}</span>
                                        @endif
                                        @if ($contractes->status == 0)
                                            <span class="badge badge-label bg-warning"><i
                                                    class="mdi mdi-circle-medium"></i>
                                                {{ trans('settings_trans.default_false') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $contractes->created_at }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>

                                                    <a class="dropdown-item edit-item-btn"
                                                        href="{{ route('contract_settings.edit', ['id' => $contractes->id]) }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                    </a>

                                                </li>
                                                <li><button data-bs-toggle="modal"
                                                        data-bs-target="#signupModal{{ $contractes->id }}"
                                                        class="dropdown-item edit-item-btn"> <i
                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                        Delete</button></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!-- edit -->

                                <div id="signupModal{{ $contractes->id }}" class="modal fade" tabindex="-1"
                                    aria-hidden="true" style="display: none;">
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
                                                        {{ $contractes->name }}</p>
                                                    <!-- Toogle to second dialog -->
                                                    <form action="{{ route('contract_settings.destroy', 'test') }}"
                                                        method="post">
                                                        {{ method_field('delete') }}
                                                        {{ csrf_field() }}
                                                        <input class="form-control" id="id" name="id"
                                                            value="{{ $contractes->id }}" type="hidden">

                                                        <button class="btn btn-warning" data-bs-target="#secondmodal"
                                                            data-bs-toggle="modal" data-bs-dismiss="modal">
                                                            {{ trans('settings_trans.massagedelete_contract') }}
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
    <div id="signupModals" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">{{ trans('settings_trans.add_contract') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-contractel="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="{{ route('contract_settings.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="mb-3">
                            <label for="name_ar" class="form-contractel">{{ trans('settings_trans.name_ar') }}</label>
                            <input type="text" class="form-control" id="name_ar" name="name_ar"
                                placeholder="{{ trans('settings_trans.name_ar_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-contractel">{{ trans('settings_trans.name_en') }}</label>
                            <input type="text" class="form-control" id="name_en" name="name_en"
                                placeholder="{{ trans('settings_trans.name_en_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-contractel">{{ trans('settings_trans.status') }}</label>
                            <select name="status" id="statusf" class="form-control" required>
                                <option value="1">{{ trans('settings_trans.default_true') }}</option>
                                <option value="0">{{ trans('settings_trans.default_false') }}</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ trans('settings_trans.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
