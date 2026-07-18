@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_schedule') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_schedule') }}</h4>
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

                    <form action="{{ route('hr_schedules.update', $schedule->id) }}" method="POST" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="row g-4"> <!-- Added g-4 for grid gap -->

                            <!-- Schedule Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ trans('hr.schedule_name') }}</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $schedule->name }}" required>
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Schedule Description -->
                            <div class="col-md-6">
                                <label for="description" class="form-label">{{ trans('hr.description') }}</label>
                                <textarea name="description" id="description" class="form-control">{{ old('description', $schedule->description) }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Schedule Active Status -->
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">{{ trans('hr.is_active') }}</label>
                                <select name="is_active" id="is_active" class="form-control" required>

                                    <option value="1" {{ $schedule->is_active == '1' ? 'selected' : '' }}>
                                        {{ trans('hr.active') }}
                                    </option>
                                    <option value="0" {{ $schedule->is_active == '0' ? 'selected' : '' }}>
                                        {{ trans('hr.inactive') }}
                                    </option>
                                </select>
                                @error('is_active')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <!-- Schedule Days (7 Days) -->
                            <div class="mb-3">
                                <h5 class="text-primary"><i class="bi bi-clock"></i> {{ trans('hr.schedule_days') }}</h5>
                                <div class="row g-3">
                                    @for ($i = 1; $i <= 7; $i++)
                                        @php
                                            // Retrieve the existing schedule day data if available
                                            $day = $schedule->days->firstWhere('day_of_week', $i);
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="border rounded p-3 bg-light">
                                                <label class="form-label fw-bold">{{ trans('hr.day_' . $i) }}</label>
                                                <input type="hidden" name="days[{{ $i }}][day_of_week]" value="{{ $i }}">
                                                
                                                <!-- Start Time -->
                                                <div class="form-floating">
                                                    <input type="time" name="days[{{ $i }}][start_time]" id="start_time_{{ $i }}" 
                                                        class="form-control" value="{{ old('days.' . $i . '.start_time', optional($day)->start_time) }}">
                                                    <label for="start_time_{{ $i }}">{{ trans('hr.start_time') }}</label>
                                                </div>

                                                <!-- End Time -->
                                                <div class="form-floating mt-2">
                                                    <input type="time" name="days[{{ $i }}][end_time]" id="end_time_{{ $i }}" 
                                                        class="form-control" value="{{ old('days.' . $i . '.end_time', optional($day)->end_time) }}">
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
                                        <span id="total-hours-preview">{{ number_format($schedule->total_hours ?? 0, 2) }} hrs</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_schedules.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
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
