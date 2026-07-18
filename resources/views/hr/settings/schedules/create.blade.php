@extends('layouts.master')

@section('title')
    {{ trans('hr.add_schedule') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.add_schedule') }}</h4>
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

                    <form action="{{ route('hr_schedules.store') }}" method="POST" class="p-4">
                        @csrf

                        <!-- Schedule Name -->
                        <div class="form-floating mb-3">
                            <input type="text" name="name" id="name" class="form-control" required placeholder="{{ trans('hr.schedule_name') }}">
                            <label for="name"><i class="bi bi-calendar3"></i> {{ trans('hr.schedule_name') }}</label>
                        </div>

                        <!-- Schedule Description -->
                        <div class="form-floating mb-3">
                            <textarea name="description" id="description" class="form-control" placeholder="{{ trans('hr.description') }}" style="height: 100px;"></textarea>
                            <label for="description"><i class="bi bi-pencil"></i> {{ trans('hr.description') }}</label>
                        </div>

                        <!-- Schedule Active Status -->
                        <div class="form-floating mb-3">
                            <select name="is_active" id="is_active" class="form-select" required>
                                <option value="1" selected>{{ trans('hr.active') }}</option>
                                <option value="0">{{ trans('hr.inactive') }}</option>
                            </select>
                            <label for="is_active"><i class="bi bi-toggle-on"></i> {{ trans('hr.is_active') }}</label>
                        </div>

                        <!-- Schedule Days (7 Days) -->
                        <div class="mb-3">
                            <h5 class="text-primary"><i class="bi bi-clock"></i> {{ trans('hr.schedule_days') }}</h5>
                            <div class="row g-3">
                                @for ($i = 1; $i <= 7; $i++)
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 bg-light">
                                            <label class="form-label fw-bold">{{ trans('hr.day_' . $i) }}</label>
                                            <input type="hidden" name="days[{{ $i }}][day_of_week]" value="{{ $i }}">
                                            <div class="form-floating">
                                                <input type="time" name="days[{{ $i }}][start_time]" id="start_time_{{ $i }}" class="form-control">
                                                <label for="start_time_{{ $i }}">{{ trans('hr.start_time') }}</label>
                                            </div>
                                            <div class="form-floating mt-2">
                                                <input type="time" name="days[{{ $i }}][end_time]" id="end_time_{{ $i }}" class="form-control">
                                                <label for="end_time_{{ $i }}">{{ trans('hr.end_time') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            
                            <!-- Total Hours Preview -->
                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-calculator"></i> 
                                    <strong>{{ trans('hr.total_hours') }}:</strong> 
                                    <span id="total-hours-preview">0.00 hrs</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-save"></i> {{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_schedules.index') }}" class="btn btn-secondary px-4"><i class="bi bi-x-circle"></i> {{ trans('buttons.cancel') }}</a>
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
        function calculateTotalHours() {
            let totalHours = 0;
            let daysWithHours = 0;

            for (let i = 1; i <= 7; i++) {
                const startTime = document.getElementById('start_time_' + i).value;
                const endTime = document.getElementById('end_time_' + i).value;

                if (startTime && endTime) {
                    const start = new Date('2000-01-01T' + startTime);
                    const end = new Date('2000-01-01T' + endTime);
                    
                    if (end > start) {
                        const diffMs = end - start;
                        const diffHours = diffMs / (1000 * 60 * 60);
                        totalHours += diffHours;
                        daysWithHours++;
                    }
                }
            }

            const averageHours = daysWithHours > 0 ? (totalHours / daysWithHours) : 0;
            document.getElementById('total-hours-preview').textContent = averageHours.toFixed(2) + ' hrs';
        }

        // Add event listeners to all time inputs
        document.addEventListener('DOMContentLoaded', function() {
            for (let i = 1; i <= 7; i++) {
                document.getElementById('start_time_' + i).addEventListener('change', calculateTotalHours);
                document.getElementById('end_time_' + i).addEventListener('change', calculateTotalHours);
            }
        });
    </script>

@endsection
