@extends('layouts.master')
@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('css')
 
    <style>
        .badge-user {
            background-color: #f3f6f9;
            color: #495057;
            border: 1px solid #dee2e6;
            margin: 2px;
            padding: 5px 8px;
            border-radius: 4px;
        }
        .table-responsive {
            padding: 0 15px;
        }
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .type-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        .main-badge {
            background-color: #0d6efd;
        }
        .sub-badge {
            background-color: #6c757d;
        }
        .add-cabinet-form {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .form-section-title {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #495057;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ trans('lockers_trans.cabinets') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">{{ trans('lockers_trans.settings') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('lockers_trans.cabinets_settings') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-checkbox-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Add Cabinet Form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="add-cabinet-form">
                    <h5 class="form-section-title">
                        <i class="ri-add-circle-line me-2"></i> {{ trans('lockers_trans.add_new_cabinet') }}
                    </h5>
                    <form action="{{ route('locker_c_settings.store') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name_ar" class="form-label">{{ trans('lockers_trans.name_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name_ar" name="name_ar"
                                    placeholder="{{ trans('lockers_trans.name_ar_enter') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="name_en" class="form-label">{{ trans('lockers_trans.name_en') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name_en" name="name_en"
                                    placeholder="{{ trans('lockers_trans.name_en_enter') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="type" class="form-label">{{ trans('lockers_trans.type') }} <span class="text-danger">*</span></label>
                                <select  class="js-example-basic-single" id="type" name="type" required>
                                    <option value="main">{{ trans('lockers_trans.main') }}</option>
                                    <option value="sub">{{ trans('lockers_trans.sub') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="currency" class="form-label">{{ trans('lockers_trans.currency') }} <span class="text-danger">*</span></label>
                                <select  class="js-example-basic-single" id="currency" name="currency" required>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="glaccounts" class="form-label">{{ trans('lockers_trans.glaccounts') }} <span class="text-danger">*</span></label>
                                <select  class="js-example-basic-single" id="glaccounts" name="glaccounts" required>
                                    @foreach ($glaccounts as $data)
                                        <option value="{{ $data->id }}">{{ $data->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="user" class="form-label">{{ trans('lockers_trans.user') }}</label>
                                <select  class="js-example-basic-single"  id="user" name="user[]" multiple>
                                    @foreach ($Users as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('lockers_trans.has_daily_limit') }}</label>
                                <select class="form-select" id="has_daily_limit" name="has_daily_limit">
                                    <option value="1">{{ trans('lockers_trans.yes') }}</option>
                                    <option value="0" selected>{{ trans('lockers_trans.no') }}</option>
                                </select>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="ri-eraser-line me-1"></i> {{ trans('lockers_trans.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> {{ trans('lockers_trans.submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cabinets List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ trans('lockers_trans.cabinets_list') }}</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cabinets-table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">{{ trans('lockers_trans.sr_no') }}</th>
                                        <th width="10%">{{ trans('lockers_trans.id') }}</th>
                                        <th>{{ trans('lockers_trans.name_ar') }}</th>
                                        <th>{{ trans('lockers_trans.name_en') }}</th>
                                        <th width="10%">{{ trans('lockers_trans.type') }}</th>
                                        <th width="10%">{{ trans('lockers_trans.currency') }}</th>
                                        <th width="10%">{{ trans('lockers_trans.glaccounts') }}</th>
      
                                        <th width="15%">{{ trans('lockers_trans.user') }}</th>
                                        <th width="10%">{{ trans('lockers_trans.has_daily_limit') }}</th>
                                        <th width="10%">{{ trans('lockers_trans.create_date') }}</th>
                                        <th width="12%">{{ trans('lockers_trans.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lockers as $index => $cabinet)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $cabinet->id }}</td>
                                            <td>{{ $cabinet->name_ar }}</td>
                                            <td>{{ $cabinet->name_en }}</td>
                                            <td>
                                                <span class="badge type-badge {{ $cabinet->type == 'main' ? 'main-badge' : 'sub-badge' }}">
                                                    {{ $cabinet->type == 'main' ? trans('lockers_trans.main') : trans('lockers_trans.sub') }}
                                                </span>
                                            </td>
                                            <td>{{ $cabinet->currencies->name??'' }}</td>
                                            <td>{{ $cabinet->glAccount->name_ar??'' }}</td>

                                            <td>
                                                @foreach ($cabinet->users as $user)
                                                    <span class="badge-user">{{ $user->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $cabinet->has_daily_limit ? trans('lockers_trans.yes') : trans('lockers_trans.no') }}</td>
                                            <td>{{ $cabinet->created_at->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-action me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $cabinet->id }}" title="{{ trans('lockers_trans.edit') }}">
                                                    <i class="ri-pencil-line"></i>
                                                </button>

                                                <button class="btn btn-danger btn-action" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $cabinet->id }}" title="{{ trans('lockers_trans.delete') }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $cabinet->id }}" tabindex="-1"
                                            aria-labelledby="editModalLabel{{ $cabinet->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel{{ $cabinet->id }}">
                                                            <i class="ri-edit-2-line me-2"></i> {{ trans('lockers_trans.edit_cabinet') }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post"
                                                            action="{{ route('locker_c_settings.update', $cabinet->id) }}">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label for="name_ar" class="form-label">{{ trans('lockers_trans.name_ar') }}</label>
                                                                    <input type="text" class="form-control" name="name_ar"
                                                                        value="{{ $cabinet->name_ar }}" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label for="name_en" class="form-label">{{ trans('lockers_trans.name_en') }}</label>
                                                                    <input type="text" class="form-control" name="name_en"
                                                                        value="{{ $cabinet->name_en }}" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label for="type" class="form-label">{{ trans('lockers_trans.type') }}</label>
                                                                    <select class="form-select" name="type" required>
                                                                        <option value="main" {{ $cabinet->type == 'main' ? 'selected' : '' }}>
                                                                            {{ trans('lockers_trans.main') }}</option>
                                                                        <option value="sub" {{ $cabinet->type == 'sub' ? 'selected' : '' }}>
                                                                            {{ trans('lockers_trans.sub') }}</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label for="currency" class="form-label">{{ trans('lockers_trans.currency') }}</label>
                                                                    <select class="form-select" name="currency" required>
                                                                        @foreach ($currencies as $currency)
                                                                            <option value="{{ $currency->id }}"
                                                                                {{ $cabinet->currency_id == $currency->id ? 'selected' : '' }}>
                                                                                {{ $currency->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label for="users" class="form-label">{{ trans('lockers_trans.user') }}</label>
                                                                    <select class="form-select select2" name="user[]" multiple>
                                                                        @foreach ($Users as $user)
                                                                            <option value="{{ $user->id }}"
                                                                                {{ in_array($user->id, $cabinet->users->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                                                {{ $user->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label for="has_daily_limit" class="form-label">{{ trans('lockers_trans.has_daily_limit') }}</label>
                                                                    <select class="form-select" name="has_daily_limit">
                                                                        <option value="1" {{ $cabinet->has_daily_limit ? 'selected' : '' }}>{{ trans('lockers_trans.yes') }}</option>
                                                                        <option value="0" {{ !$cabinet->has_daily_limit ? 'selected' : '' }}>{{ trans('lockers_trans.no') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer border-top-0">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    {{ trans('lockers_trans.cancel') }}
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    {{ trans('lockers_trans.update') }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $cabinet->id }}" tabindex="-1"
                                            aria-labelledby="deleteModalLabel{{ $cabinet->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title text-danger" id="deleteModalLabel{{ $cabinet->id }}">
                                                            <i class="ri-alert-line me-2"></i> {{ trans('lockers_trans.confirm_delete') }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>{{ trans('lockers_trans.delete_message', ['name' => $cabinet->name_ar]) }}</p>
                                                        <p class="text-muted">{{ trans('lockers_trans.delete_warning') }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            {{ trans('lockers_trans.cancel') }}
                                                        </button>
                                                        <form method="post" action="{{ route('locker_c_settings.destroy', $cabinet->id) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                {{ trans('lockers_trans.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#cabinets-table').DataTable({
                responsive: true,
                language: {
                    url: "{{ URL::asset('assets/js/ar.json') }}"
                },
                columnDefs: [
                    { orderable: false, targets: [9] }
                ]
            });

            // Initialize Select2
            $('.select2').select2({
                placeholder: "{{ trans('lockers_trans.select_users') }}",
                allowClear: true,
                width: '100%'
            });

            // Show success message after form submission
            @if(Session::has('success'))
                toastr.success("{{ session('success') }}");
            @endif

            // Show error message if exists
            @if(Session::has('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
@endsection