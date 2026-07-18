@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_attendance') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_attendance') }}</h4>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Start Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('hr_attendance.update', $attendance->id) }}" method="POST" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="row g-4"> <!-- Added g-4 for grid gap -->

                            <!-- Employee Selection -->
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">{{ trans('hr.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="js-example-basic-single">
                                    <option value="">--Select--</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $attendance->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Contract Start and End Dates -->
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">{{ trans('hr.check_in') }}</label>
                                <input type="datetime-local" name="check_in" id="check_in" class="form-control"
                                    value="{{ old('check_in', date('Y-m-d\TH:i', strtotime($attendance->check_in))) }}" required>
                                @error('check_in')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="check_out" class="form-label">{{ trans('hr.check_out') }}</label>
                                <input type="datetime-local" name="check_out" id="check_out" class="form-control"
                                    value="{{ old('check_out', date('Y-m-d\TH:i', strtotime($attendance->check_out))) }}" required>
                                @error('check_out')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Worked Hours -->
                            <div class="col-md-6">
                                <label for="worked_hours" class="form-label">{{ trans('hr.worked_hours') }}</label>
                                <input type="text" id="worked_hours" class="form-control" value="{{ $attendance->worked_hours }}" readonly>
                            </div>

                            <!-- Penalty -->
                            <div class="col-md-6">
                                <label for="penalty" class="form-label">{{ trans('hr.penalty') }}</label>
                                <input type="number" name="penalty" id="penalty" class="form-control" 
                                    value="{{ old('penalty', $attendance->penalty) }}" 
                                    step="0.25" min="0" max="10" required>
                                <small class="form-text text-muted">
                                    {{ trans('hr.penalty_help') }}
                                </small>
                                @error('penalty')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.update') }}</button>
                            <a href="{{ route('hr_attendance.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
    <style>
        .status-bar {
            gap: 10px;
        }

        .status-item {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            background-color: #f0f0f0;
            transition: all 0.3s;
        }

        .status-item.active {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .status-item:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const workedHoursInput = document.getElementById('worked_hours');

            function calculateWorkedHours() {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);

                if (!isNaN(checkIn) && !isNaN(checkOut) && checkOut > checkIn) {
                    const diffInMs = checkOut - checkIn; // Difference in milliseconds
                    const diffInHours = (diffInMs / (1000 * 60 * 60)).toFixed(2); // Convert to hours and round to 2 decimal places
                    workedHoursInput.value = diffInHours;
                } else {
                    workedHoursInput.value = ''; // Clear the field if invalid
                }
            }

            checkInInput.addEventListener('input', calculateWorkedHours);
            checkOutInput.addEventListener('input', calculateWorkedHours);
        });
    </script>
@endsection 