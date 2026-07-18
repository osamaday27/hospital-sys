@extends('layouts.master')

@section('title')
    {{ trans('main_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('lockers_trans.reasonspartialpayment') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">{{ trans('lockers_trans.settings') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('lockers_trans.reasonspartialpayment_settings') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @foreach (['success', 'updated', 'deleted', 'error'] as $msg)
        @if (session()->has($msg))
            <div class="alert alert-{{ $msg == 'error' ? 'danger' : 'success' }} alert-dismissible alert-label-icon rounded-label shadow fade show" role="alert">
                <i class="{{ $msg == 'error' ? 'ri-close-circle-line' : ($msg == 'deleted' ? 'ri-delete-bin-line' : 'ri-check-line') }} label-icon"></i>
                <strong>{{ __('messages.' . ($msg == 'error' ? 'error.failed' : 'success.' . 'created')) }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ trans('lockers_trans.reasonspartialpayment_list') }}</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReasonModal">
                        {{ trans('lockers_trans.add_new_reasonspartialpayment') }}
                    </button>
                </div>

                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('lockers_trans.id') }}</th>
                                <th>{{ trans('lockers_trans.name_ar') }}</th>
                                <th>{{ trans('lockers_trans.name_en') }}</th>
                                <th>{{ trans('lockers_trans.create_date') }}</th>
                                <th>{{ trans('lockers_trans.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reasons as $index => $reason)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $reason->id }}</td>
                                    <td>{{ $reason->name_ar }}</td>
                                    <td>{{ $reason->name_en }}</td>
                                    <td>{{ $reason->created_at }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $reason->id }}">
                                            {{ trans('lockers_trans.edit') }}
                                        </button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $reason->id }}">
                                            {{ trans('lockers_trans.delete') }}
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $reason->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post" action="{{ route('reasonspartialpayment.update', $reason->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ trans('lockers_trans.edit') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ trans('lockers_trans.name_ar') }}</label>
                                                        <input type="text" class="form-control" name="name_ar" value="{{ $reason->name_ar }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ trans('lockers_trans.name_en') }}</label>
                                                        <input type="text" class="form-control" name="name_en" value="{{ $reason->name_en }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ trans('lockers_trans.status') }}</label>
                                                        <select class="form-control" name="status">
                                                            <option value="active" {{ $reason->status == 'active' ? 'selected' : '' }}>
                                                                {{ trans('lockers_trans.active') }}
                                                            </option>
                                                            <option value="inactive" {{ $reason->status == 'inactive' ? 'selected' : '' }}>
                                                                {{ trans('lockers_trans.inactive') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">{{ trans('lockers_trans.update') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $reason->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post" action="{{ route('reasonspartialpayment.destroy', $reason->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ trans('lockers_trans.confirm_delete') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ trans('lockers_trans.delete_message', ['name' => $reason->name_ar]) }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">{{ trans('lockers_trans.delete') }}</button>
                                                </div>
                                            </form>
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

    <!-- Add Reason Modal -->
    <div id="addReasonModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('reasonspartialpayment.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ trans('lockers_trans.add_new_reasonspartialpayment') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('lockers_trans.name_ar') }}</label>
                            <input type="text" class="form-control" name="name_ar" placeholder="{{ trans('lockers_trans.name_ar_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ trans('lockers_trans.name_en') }}</label>
                            <input type="text" class="form-control" name="name_en" placeholder="{{ trans('lockers_trans.name_en_enter') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ trans('lockers_trans.status') }}</label>
                            <select class="form-control" name="status">
                                <option value="active">{{ trans('lockers_trans.active') }}</option>
                                <option value="inactive">{{ trans('lockers_trans.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('lockers_trans.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
