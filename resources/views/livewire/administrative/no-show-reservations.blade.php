<div>
    <div class="card shadow border-0 rounded-4">
        <!-- Header -->
        <div class="card-header bg-gradient bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0  text-primary">
                <i class="mdi mdi-account-off-outline me-2"></i> 
                {{ __('call_center_trans.no_show_reservations') }}
            </h5>
            <div>
                <button wire:click="setTab('pending')" class="btn btn-light btn-sm {{ $tab==='pending'?'active shadow':'' }}">
                    <i class="mdi mdi-clock-alert"></i> {{ __('call_center_trans.pending_reasons') }}
                </button>
                <button wire:click="setTab('recorded')" class="btn btn-light btn-sm {{ $tab==='recorded'?'active shadow':'' }}">
                    <i class="mdi mdi-check-decagram"></i> {{ __('call_center_trans.recorded_reasons') }}
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">
            {{-- رسالة نجاح --}}
            @if(session()->has('success'))
                <div class="alert alert-success shadow-sm rounded-pill">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            {{-- الفلاتر --}}
            <div class="row g-2 mb-4">
                <div class="col-md-2">
                    <input type="date" wire:model="date_from" class="form-control" title="من">
                </div>
                <div class="col-md-2">
                    <input type="date" wire:model="date_to" class="form-control" title="إلى">
                </div>
                <div class="col-md-8">
                    <input type="text" wire:model="search" class="form-control" placeholder="{{ __('call_center_trans.search_patient') }}">
                </div>
            </div>

            {{-- الجدول --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-info text-black">
                        <tr>
                            <th>#</th>
                            <th>{{ __('call_center_trans.patient_name') }}</th>
                            <th>{{ __('call_center_trans.phone') }}</th>
                            <th>{{ __('call_center_trans.gender') }}</th>
                            <th>{{ __('call_center_trans.contract') }}</th>
                            <th>{{ __('call_center_trans.last_visit') }}</th>
                            <th>{{ __('call_center_trans.clinic') }}</th>
                            <th>{{ __('call_center_trans.doctor') }}</th>
                            <th>{{ __('call_center_trans.visit_date') }}</th>
                            <th>{{ __('call_center_trans.status') }}</th>
                            <th>{{ __('call_center_trans.absence_reason') }}</th>
                            <th>{{ __('call_center_trans.notes') }}</th>
                            <th>{{ __('call_center_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $res)
                            <tr>
                                <td>{{ $res->id }}</td>
                                <td>
                                    <i class="mdi mdi-account-circle-outline text-primary"></i> 
                                    {{ $res->patient->full_name ?? '-' }}
                                </td>
                                <td>
                                    <i class="mdi mdi-phone text-success"></i> 
                                    {{ $res->patient->phone_number ?? '-' }} / {{ $res->patient->phone_number2 ?? '-' }}
                                </td>
                                <td>
                                    @if($res->patient->gender == 'male')
                                        <span class="badge bg-info">{{ __('call_center_trans.male') }}</span>
                                    @else
                                        <span class="badge bg-pink">{{ __('call_center_trans.female') }}</span>
                                    @endif
                                </td>
                                <td>{{ $res->patient->contract->name ?? '-' }}</td>
                                <td>{{ $res->patient->lastVisit->date_visit ?? '-' }}</td>
                                <td>{{ $res->clinic->name_ar ?? '-' }}</td>
                                <td>{{ $res->doctor->full_name ?? '-' }}</td>
                                <td>{{ $res->date_visit }}</td>
                                <td>
                                    <span class="badge bg-danger">
                                        <i class="mdi mdi-close-octagon"></i> {{ $res->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($tab==='pending')
                                        <select wire:model="cancel_reason_id" class="form-select">
                                            <option value="">{{ __('call_center_trans.choose') }}</option>
                                            @foreach($reasons as $reason)
                                                <option value="{{ $reason->id }}">{{ $reason->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $res->absenceReason->name_ar ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($tab==='pending')
                                        <input type="text" wire:model="cancel_note" class="form-control" placeholder="{{ __('call_center_trans.notes') }}">
                                    @else
                                        {{ $res->cancel_note ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    @if($tab==='pending')
                                        <button wire:click="saveReason({{ $res->id }})" class="btn btn-success btn-sm">
                                            <i class="mdi mdi-content-save"></i> {{ __('call_center_trans.save') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-3">
                                    <i class="mdi mdi-information-outline"></i> {{ __('call_center_trans.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $reservations->links() }}
        </div>
    </div>
</div>
