<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">{{ $room->name_ar }}</h5>
        </div>

        <div class="card-body">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <strong>{{ session('success') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="mb-3">
                <label for="duration" class="form-label">{{ trans('surgical_trans.duration') }}</label>
                <input type="number" wire:model.defer="duration" class="form-control"
                    placeholder="مدة العملية بالدقائق">
                @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            @foreach ($daysList as $dayKey => $dayName)
                @php
                    $dayData = $schedule[$dayKey] ?? null;
                @endphp
                <div class="row mb-3 align-items-center">
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="days.{{ $dayKey }}"
                                wire:change="toggleDay('{{ $dayKey }}')" id="switch_{{ $dayKey }}">
                            <label class="form-check-label fw-bold"
                                for="switch_{{ $dayKey }}">{{ $dayName }}</label>
                        </div>
                    </div>

                    @if (isset($days[$dayKey]) && $days[$dayKey])
                        <div class="col-md-3">
                            <label>{{ trans('surgical_trans.start_time') }}</label>
                            <input type="time" wire:model.defer="schedule.{{ $dayKey }}.start_time"
                                class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>{{ trans('surgical_trans.end_time') }}</label>
                            <input type="time" wire:model.defer="schedule.{{ $dayKey }}.end_time"
                                class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>{{ trans('outpatient_trans.status') }}</label>
                            <select wire:model.defer="schedule.{{ $dayKey }}.status" class="form-select">
                                <option value="active">{{ trans('general_trans.active') }}</option>
                                <option value="inactive">{{ trans('general_trans.inactive') }}</option>
                            </select>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="text-start mt-3">
                <button wire:click="save" class="btn btn-primary">
                    <i class="ri-save-3-line me-1"></i>{{ trans('buttons.save') }}
                </button>
            </div>
        </div>
    </div>
</div>
