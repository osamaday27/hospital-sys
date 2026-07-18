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
                        <li class="breadcrumb-item active">{{ trans('settings_trans.employees_settings') }}</li>
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
                    <h5 class="card-title mb-0">{{ trans('settings_trans.employees_settings') }}</h5>
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
                        type="button">{{ trans('settings_trans.add_employees') }}</button>
                </div>


                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false">{{ trans('settings_trans.sr_no') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.id') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.name') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.email') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.phone') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.date_of_birth') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.id_national') }}</th>
                                <th data-ordering="false">{{ trans('settings_trans.status') }}</th>
                                <th>{{ trans('settings_trans.create_date') }}</th>
                                <th>{{ trans('settings_trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach ($employees as $data)
                                <tr>
                                    <?php $i++; ?>
                                    <td>{{ $i }}</td>
                                    <td>{{ $data->id }}</td>
                                    <td>
                                        <div class="flex-shrink-0" style="display: ruby-base;">
                                            <div>
                                                <img class="image avatar-xs rounded-circle shadow" alt=""
                                                    src="@if ($data->img) {{ asset($data->img) }} @else {{ asset('assets/images/users/user-dummy-img.jpg') }} @endif">
                                            </div>
                                        </div>
                                        {{ $data->name }}
                                    </td>
                                    <td>{{ $data->email }}</td>
                                    <td>{{ $data->phone }}</td>
                                    <td>{{ $data->date_of_birth }}</td>
                                    <td>{{ $data->id_number }}</td>

                                    <td>
                                        @if ($data->status == 1)
                                            <span class="badge badge-label bg-success"><i class="mdi mdi-circle-medium"></i>
                                                {{ trans('settings_trans.default_true') }}</span>
                                        @endif
                                        @if ($data->status == 0)
                                            <span class="badge badge-label bg-warning"><i class="mdi mdi-circle-medium"></i>
                                                {{ trans('settings_trans.default_false') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><button data-bs-toggle="modal"
                                                        data-bs-target="#signupModals{{ $data->id }}"
                                                        class="dropdown-item edit-item-btn"><i
                                                            class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Edit</button></li>
                                                <li><button data-bs-toggle="modal"
                                                        data-bs-target="#signupModal{{ $data->id }}"
                                                        class="dropdown-item edit-item-btn"> <i
                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                        Delete</button></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!-- edit-->
                                <div id="signupModals{{ $data->id }}" class="modal fade" tabindex="-1"
                                    aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content border-0 overflow-hidden">
                                            <div class="modal-header p-3">
                                                <label class="card-title mb-0">{{ trans('settings_trans.update_employ') }}
                                                </label>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('employees.update', 'test') }}" method="post"
                                                    enctype="multipart/form-data">
                                                    {{ method_field('patch') }}
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_ar"
                                                                    class="form-label">{{ trans('settings_trans.name_ar') }}</label>
                                                                <input class="form-control" id="id" name="id"
                                                                    value="{{ $data->id }}" type="hidden">
                                                                <input type="text" class="form-control" id="name_ar"
                                                                    name="name_ar"
                                                                    value="{{ $data->getTranslation('name', 'ar') }}"
                                                                    placeholder="{{ trans('settings_trans.name_ar_enter') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.name_en') }}</label>
                                                                <input type="text" class="form-control" id="name_en"
                                                                    name="name_en"
                                                                    value="{{ $data->getTranslation('name', 'en') }}"
                                                                    placeholder="{{ trans('settings_trans.name_en_enter') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_ar"
                                                                    class="form-label">{{ trans('settings_trans.phone') }}</label>
                                                                <input type="text" class="form-control" id="phone"
                                                                    name="phone" value="{{ $data->phone }}"
                                                                    placeholder="{{ trans('settings_trans.phone_enter') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.email') }}</label>
                                                                <input type="text" class="form-control" id="email"
                                                                    name="email" value="{{ $data->email }}"
                                                                    placeholder="{{ trans('settings_trans.email_enter') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_ar"
                                                                    class="form-label">{{ trans('settings_trans.date_of_birth') }}</label>
                                                                <input type="date" class="form-control"
                                                                    id="date_of_birth" name="date_of_birth"
                                                                    value="{{ $data->date_of_birth }}"
                                                                    placeholder="{{ trans('settings_trans.date_of_birth_enter') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.id_number') }}</label>
                                                                <input type="number" class="form-control" id="id_number"
                                                                    name="id_number" value="{{ $data->id_number }}"
                                                                    placeholder="{{ trans('settings_trans.id_number_enter') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.job') }}</label>
                                                                <select name="job" id="job" class="form-select"
                                                                    data-choices data-choices-sorting="true">
                                                                    <option value="1" disabled>
                                                                        {{ trans('settings_trans.job_selected') }}</option>
                                                                    <option value="doctor"
                                                                        @if ($data->job == 'doctor') selected @endif>
                                                                        {{ trans('settings_trans.doctor') }}</option>
                                                                    <option value="accountant"
                                                                        @if ($data->job == 'accountant') selected @endif>
                                                                        {{ trans('settings_trans.accountant') }}</option>
                                                                    <option value="reception"
                                                                        @if ($data->job == 'reception') selected @endif>
                                                                        {{ trans('settings_trans.reception') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="ForminputState"
                                                                    class="form-label">{{ trans('settings_trans.specialty_select') }}</label>
                                                                <select name="specialty" id="specialty"
                                                                    class="form-select" data-choices
                                                                    data-choices-sorting="true">
                                                                    <option value="" selected disabled>
                                                                        {{ trans('settings_trans.specialty_select') }}
                                                                    </option>
                                                                    @foreach ($specialty as $data2)
                                                                        <option value="{{ $data2->id }}"
                                                                            @if ($data->specialization == $data2->id) selected @endif>
                                                                            {{ $data2->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_ar"
                                                                    class="form-label">{{ trans('settings_trans.salary') }}</label>
                                                                <input type="number" class="form-control" id="salary"
                                                                    name="salary" value="{{ $data->salary }}"
                                                                    placeholder="{{ trans('settings_trans.salary') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.address') }}</label>
                                                                <input type="text" class="form-control" id="address"
                                                                    name="address" value="{{ $data->address }}"
                                                                    placeholder="{{ trans('settings_trans.address_enter') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_ar"
                                                                    class="form-label">{{ trans('settings_trans.img_employee') }}</label>
                                                                <input type="file" class="form-control"
                                                                    id="img_employee" name="img_employee"
                                                                    placeholder="{{ trans('settings_trans.img_employee') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.img_id1') }}</label>
                                                                <input type="file" class="form-control" id="img_id1"
                                                                    name="img_id1"
                                                                    placeholder="{{ trans('settings_trans.address_enter') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_en"
                                                                    class="form-label">{{ trans('settings_trans.img_id2') }}</label>
                                                                <input type="file" class="form-control" id="img_id2"
                                                                    name="img_id2"
                                                                    placeholder="{{ trans('settings_trans.address_enter') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label for="name_ar"
                                                                    class="form-label">{{ trans('settings_trans.note') }}</label>
                                                                <textarea type="number" class="form-control" id="note" name="note"
                                                                    placeholder="{{ trans('settings_trans.note') }}">{{ $data->note }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('settings_trans.submit') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>-->
                                    <div id="signupModal{{ $data->id }}" class="modal fade bs-example-modal-xl"
                                        tabindex="-1" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 overflow-hidden">
                                                <div class="modal-header p-3">
                                                    <h4 class="card-title mb-0">
                                                        {{ trans('settings_trans.massagedelete_nationalities') }}</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-5">
                                                    <lord-icon src="{{ URL::asset('assets/images/icon/tdrtiskw.json') }}"
                                                        trigger="loop" colors="primary:#f7b84b,secondary:#405189"
                                                        style="width:130px;height:130px">
                                                    </lord-icon>
                                                    <div class="mt-4 pt-4">
                                                        <h4>{{ trans('settings_trans.massagedelete_d') }}!</h4>
                                                        <p class="text-muted">
                                                            {{ trans('settings_trans.massagedelete_p') }}{{ $data->name }}
                                                        </p>
                                                        <!-- Toogle to second dialog -->
                                                        <form action="{{ route('employees.destroy', 'test') }}"
                                                            method="post">
                                                            {{ method_field('delete') }}
                                                            {{ csrf_field() }}
                                                            <input class="form-control" id="id" name="id"
                                                                value="{{ $data->id }}" type="hidden">

                                                            <button class="btn btn-warning" data-bs-target="#secondmodal"
                                                                data-bs-toggle="modal" data-bs-dismiss="modal">
                                                                {{ trans('settings_trans.massagedelete_nationalities') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="signupModals" class="modal fade bs-example-modal-xl" tabindex="-1" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0">{{ trans('settings_trans.add_employees') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('employees.store') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('settings_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        placeholder="{{ trans('settings_trans.name_ar_enter') }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        placeholder="{{ trans('settings_trans.name_en_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">{{ trans('settings_trans.phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="{{ trans('settings_trans.phone_enter') }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.email') }}</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="{{ trans('settings_trans.email_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('settings_trans.date_of_birth') }}</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        placeholder="{{ trans('settings_trans.date_of_birth_enter') }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.id_number') }}</label>
                                    <input type="number" class="form-control" id="id_number" name="id_number"
                                        placeholder="{{ trans('settings_trans.id_number_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('settings_trans.job') }}</label>
                                    <select name="job" id="job" class="form-select" data-choices
                                        data-choices-sorting="true">
                                        <option value="1" selected disabled>
                                            {{ trans('settings_trans.job_selected') }}</option>
                                        <option value="doctor">{{ trans('settings_trans.doctor') }}</option>
                                        <option value="accountant">{{ trans('settings_trans.accountant') }}</option>
                                        <option value="reception">{{ trans('settings_trans.reception') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="ForminputState"
                                        class="form-label">{{ trans('settings_trans.specialty_select') }}</label>
                                    <select name="specialty" id="specialty" class="form-select" data-choices
                                        data-choices-sorting="true">
                                        <option value="" selected disabled>
                                            {{ trans('settings_trans.specialty_select') }}
                                        </option>
                                        @foreach ($specialty as $data)
                                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('settings_trans.salary') }}</label>
                                    <input type="number" class="form-control" id="salary" name="salary"
                                        placeholder="{{ trans('settings_trans.salary') }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.address') }}</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        placeholder="{{ trans('settings_trans.address_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('settings_trans.img_employee') }}</label>
                                    <input type="file" class="form-control" id="img_employee" name="img_employee"
                                        placeholder="{{ trans('settings_trans.img_employee') }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.img_id1') }}</label>
                                    <input type="file" class="form-control" id="img_id1" name="img_id1"
                                        placeholder="{{ trans('settings_trans.address_enter') }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('settings_trans.img_id2') }}</label>
                                    <input type="file" class="form-control" id="img_id2" name="img_id2"
                                        placeholder="{{ trans('settings_trans.address_enter') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">{{ trans('settings_trans.note') }}</label>
                                    <textarea type="number" class="form-control" id="note" name="note"
                                        placeholder="{{ trans('settings_trans.note') }}"></textarea>
                                </div>
                            </div>
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
