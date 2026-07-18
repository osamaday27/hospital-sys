<div class="container-fluid py-4" dir="rtl">
    <div class="row g-4">

        {{-- 🔔 رسائل فلاش --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="mdi mdi-check-circle-outline me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ✅ عرض الأخطاء المجمعة في آخر الشاشة --}}
        @if (session()->has('validation_errors'))
            <div class="alert alert-danger mt-4">
                <h6 class="fw-bold"><i class="mdi mdi-alert-circle-outline"></i> {{ __('surgical_trans.validation_error') }}</h6>
                <ul class="mb-0">
                    @foreach (session('validation_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🧾 بيانات أساسية --}}
        <div class="col-md-12">
            <div class="card border-primary shadow-lg rounded">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="mdi mdi-plus-circle me-2"></i>
                    <span>{{ trans('doctor_trans.add_surgery_request') }}</span>
                </div>
                <div class="card-body bg-light">

                    <form wire:submit.prevent="submit">
                        <input type="hidden" wire:model="id_patient" />

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-calendar me-1 text-dark"></i>
                                    {{ __('surgical_trans.booking_date') }}
                                </label>
                                <input type="date" wire:model="booking_date" class="form-control">
                                @error('booking_date')
                                    <small class="text-danger d-block mt-1">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-text-box-edit-outline me-1 text-dark"></i>
                                    {{ __('surgical_trans.note') }}
                                </label>
                                <textarea wire:model="notes" class="form-control" rows="1" placeholder="{{ __('surgical_trans.note_placeholder') }}"></textarea>
                                @error('notes')
                                    <small class="text-danger d-block mt-1">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- 🔎 البحث عن العمليات --}}
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="mdi mdi-magnify text-secondary"></i>
                            </span>
                            <input type="text" wire:model.debounce.500ms="surgerySearch"
                                   class="form-control border-start-0"
                                   placeholder="{{ __('surgical_trans.search_surgery') }}">
                        </div>

                        {{-- ⚙️ اختيار العمليات المتعددة + الجهة --}}
                        <div class="row g-3">
                            @forelse($surgeries as $srv)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card h-100 border-0 shadow-sm hover-shadow
                                       @error('surgery_sides.' . $srv->id) border border-danger bg-danger-subtle @else bg-light @enderror">
                                        <div class="card-body">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-2" type="checkbox"
                                                       wire:model="surgery_ids" value="{{ $srv->id }}"
                                                       id="srv_{{ $srv->id }}">
                                                <label for="srv_{{ $srv->id }}" class="fw-semibold">
                                                    <i class="mdi mdi-hospital-box-outline text-secondary me-1"></i>
                                                    {{ $srv->getTranslation('name', app()->getLocale()) ?? $srv->name }}
                                                </label>
                                            </div>

                                            <div class="mt-2">
                                                <label class="form-label small fw-semibold">{{ __('surgical_trans.side') }}</label>
                                                <select wire:model="surgery_sides.{{ $srv->id }}"
                                                        class="form-select form-select-sm @error('surgery_sides.' . $srv->id) is-invalid @else bg-info-subtle @enderror">
                                                    <option value="">{{ __('surgical_trans.select') }}</option>
                                                    <option value="right">{{ __('surgical_trans.right') }}</option>
                                                    <option value="left">{{ __('surgical_trans.left') }}</option>
                                                    <option value="both">{{ __('surgical_trans.both') }}</option>
                                                </select>
                                                @error('surgery_sides.' . $srv->id)
                                                    <small class="text-danger d-block mt-1">
                                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                                    </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    {{ __('surgical_trans.no_surgeries') }}
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-3 d-flex justify-content-center">
                            {{ $surgeries->links() }}
                        </div>

                        <div class="mt-3">
                            @error('surgery_ids')
                                <small class="text-danger d-block">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-content-save-check-outline me-1"></i>
                                {{ __('surgical_trans.save_booking') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        {{-- 📜 الحجوزات السابقة للمريض --}}
        <div class="col-md-12">
            <div class="card border-secondary shadow-lg rounded">
                <div class="card-header bg-secondary text-white d-flex align-items-center">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    <span>{{ trans('doctor_trans.previous_examinations') }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>العمليات</th>
                                <th>التاريخ</th>
                                <th>الملاحظات</th>
                                <th>تم الطلب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $row)
                                <tr>
                                    <td>
                                        @if($row->services && $row->services->count())
                                            @foreach($row->services as $svc)
                                                <span class="badge bg-info-subtle text-dark border">
                                                    {{ $svc->getTranslation('name', app()->getLocale()) ?? $svc->name }}
                                                    @if(!empty($svc->pivot?->side))
                                                        <small class="text-muted">({{ $svc->pivot->side }})</small>
                                                    @endif
                                                </span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $row->booking_date }}</td>
                                    <td>{{ $row->notes ?? '-' }}</td>
                                    <td>{{ $row->user->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد طلبات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
