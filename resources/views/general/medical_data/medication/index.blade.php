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
                        <li class="breadcrumb-item active">{{ trans('settings_trans.drugs_settings') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.medical_settings') }}</h5>
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
                    <button data-bs-toggle="modal" data-bs-target="#signupModals"
                        class="btn btn-primary waves-effect waves-light"
                        type="button">{{ trans('settings_trans.add_drugs_settings') }}</button>
                </div>


                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false">{{ trans('settings_trans.sr_no') }}</th>
                                <th>{{ trans('settings_trans.action') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.id') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.name_ar') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.name_en') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.effective_material') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.description') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.preparations') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.default_dose_unit') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.default_dose') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.method_taking_medications') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.start_dose') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.end_dose') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.duration') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.repeat') }}</th>
                                <th>{{ trans('settings_trans.create_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach ($drugs_settings as $drugs_settingses)
                                <tr>

                                    <?php $i++; ?>
                                    <td>{{ $i }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>

                                                    <a class="dropdown-item edit-item-btn"
                                                        href="{{ route('drug_settings.edit', ['id' => $drugs_settingses->id]) }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                    </a>

                                                </li>
                                                <li><button data-bs-toggle="modal"
                                                        data-bs-target="#signupModal{{ $drugs_settingses->id }}"
                                                        class="dropdown-item edit-item-btn"> <i
                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                        Delete</button></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>{{ $drugs_settingses->id }}</td>
                                    <td>{{ $drugs_settingses->getTranslation('name', 'ar') }}</td>
                                    <td>{{ $drugs_settingses->getTranslation('name', 'en') }}</td>
                                    <td>{{ $drugs_settingses->effective_material }}</td>
                                    <td>{{ $drugs_settingses->description }}</td>
                                    <td>{{ $drugs_settingses->preparations }}</td>
                                    <td>{{ $drugs_settingses->default_dose_unit }}</td>
                                    <td>{{ $drugs_settingses->default_dose }}</td>
                                    <td>{{ $drugs_settingses->method_taking_medications }}</td>
                                    <td>{{ $drugs_settingses->start_dose }}</td>
                                    <td>{{ $drugs_settingses->end_dose }}</td>
                                    <td>{{ $drugs_settingses->duration }}</td>
                                    <td>{{ $drugs_settingses->repeat }}</td>
                                    <td>{{ $drugs_settingses->created_at }}</td>

                                </tr>
                                <!-- edit -->

                                <div id="signupModal{{ $drugs_settingses->id }}" class="modal fade" tabindex="-1"
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
                                                    <p class="text-muted">
                                                        {{ trans('settings_trans.massagedelete_p') }}{{ $drugs_settingses->name }}
                                                    </p>
                                                    <!-- Toogle to second dialog -->
                                                    <form action="{{ route('drug_settings.destroy', 'test') }}"
                                                        method="post">
                                                        {{ method_field('delete') }}
                                                        {{ csrf_field() }}
                                                        <input class="form-control" id="id" name="id"
                                                            value="{{ $drugs_settingses->id }}" type="hidden">

                                                        <button class="btn btn-warning" data-bs-target="#secondmodal"
                                                            data-bs-toggle="modal" data-bs-dismiss="modal">
                                                            {{ trans('settings_trans.massagedelete_drug') }}
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
                    <h4 class="card-title mb-0">{{ trans('settings_trans.add_drugs_settings') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="{{ route('drug_settings.store') }}" method="post">
                        {{ csrf_field() }}

                        <div class="mb-3">
                            <label for="name_ar" class="form-label">{{ trans('settings_trans.name_ar') }}</label>
                            <input type="text" class="form-control" id="name_ar" name="name_ar"
                                placeholder="{{ trans('settings_trans.name_ar_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ trans('settings_trans.name_en') }}</label>
                            <input type="text" class="form-control" id="name_en" name="name_en"
                                placeholder="{{ trans('settings_trans.name_en_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ trans('settings_trans.preparations') }}</label>
                            <input type="text" class="form-control" id="preparations" name="preparations"
                                placeholder="{{ trans('settings_trans.preparations_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ trans('settings_trans.description') }}</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="{{ trans('settings_trans.description_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label for="name_en"
                                class="form-label">{{ trans('settings_trans.effective_material') }}</label>
                            <input type="text" class="form-control" id="effective_material" name="effective_material"
                                placeholder="{{ trans('settings_trans.effective_material_enter') }}">
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.default_dose_unit') }}</label>
                                    <select class="js-example-basic-single" name="default_dose_unit">
                                        @foreach ($units_settings as $units_settinges)
                                            <option value="{{ $units_settinges->id }}">{{ $units_settinges->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="start_dose"
                                        class="form-label">{{ trans('settings_trans.default_dose') }}</label>
                                    <input type="text" class="form-control" id="default_dose" name="default_dose"
                                        placeholder="{{ trans('settings_trans.default_dose_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="start_dose"
                                        class="form-label">{{ trans('settings_trans.start_dose') }}</label>
                                    <input type="date" class="form-control" id="start_dose" name="start_dose"
                                        placeholder="{{ trans('settings_trans.start_dose_enter') }}">
                                </div>
                                <div class="col-6">
                                    <label for="end_dose"
                                        class="form-label">{{ trans('settings_trans.end_dose') }}</label>
                                    <input type="date" class="form-control" id="end_dose" name="end_dose"
                                        placeholder="{{ trans('settings_trans.end_dose_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="start_dose"
                                        class="form-label">{{ trans('settings_trans.duration') }}</label>
                                    <input type="text" class="form-control" id="duration" name="duration"
                                        placeholder="{{ trans('settings_trans.duration_enter') }}">
                                </div>
                                <div class="col-6">
                                    <label for="end_dose" class="form-label">{{ trans('settings_trans.repeat') }}</label>
                                    <input type="text" class="form-control" id="repeat" name="repeat"
                                        placeholder="{{ trans('settings_trans.repeat_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="method_taking_medications"
                                class="form-label">{{ trans('settings_trans.method_taking_medications') }}</label>
                            <input type="text" class="form-control" id="method_taking_medications"
                                name="method_taking_medications"
                                placeholder="{{ trans('settings_trans.method_taking_medications_enter') }}">
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
