{{-- resources/views/livewire/costing/run/cost-run-wizard.blade.php --}}
<div class="container-fluid py-3" dir="rtl" wire:ignore.self>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="×"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="×"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="mdi mdi-play-circle-outline me-2"></i> {{ __('costing_trans.title') }}
            </h5>
        </div>

        <div class="card-body">

            {{-- Steps indicator --}}
            @php
                $steps = [
                    1 => __('costing_trans.step1'),
                    2 => __('costing_trans.step2'),
                    3 => __('costing_trans.step3'),
                    4 => __('costing_trans.step4'),
                ];
            @endphp
            <div class="row gx-2 gy-2 mb-4">
                @foreach($steps as $no => $label)
                    <div class="col-12 col-md-3">
                        <div class="p-3 text-center rounded-3
                            {{ $step >= $no ? 'bg-primary text-white' : 'bg-secondary text-white-50' }}">
                            <div class="fw-bold">{{ $no }}.</div>
                            <div class="small">{{ $label }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- STEP 1: الفترة + النطاق --}}
            @if($step === 1)
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('costing_trans.period_start') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" wire:model="period_start">
                        @error('period_start') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('costing_trans.period_end') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" wire:model="period_end">
                        @error('period_end') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('costing_trans.scope_type') }}</label>
                        <select class="form-select" wire:model="scope_type">
                            <option value="hospital">{{ __('costing_trans.scope_hospital') }}</option>
                            <option value="centers">{{ __('costing_trans.scope_centers') }}</option>
                            <option value="services" disabled>{{ __('costing_trans.scope_services') }}</option>
                        </select>
                        @error('scope_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- اختيار المراكز عند اختيار النطاق = مراكز --}}
                    @if($scope_type === 'centers')
                        <div class="col-12">
                            <label class="form-label">{{ __('costing_trans.choose_centers') }}</label>
                            <div class="row g-2">
                                @forelse($centers as $cc)
                                    <div class="col-12 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   id="cc{{ $cc->id }}"
                                                   wire:model="scope_ids"
                                                   value="{{ $cc->id }}">
                                            <label class="form-check-label" for="cc{{ $cc->id }}">
                                                <span class="badge bg-secondary">{{ $cc->code }}</span>
                                                — {{ $cc->getTranslation('name', app()->getLocale()) }}
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-muted small">{{ __('costing_trans.no_data') }}</div>
                                    </div>
                                @endforelse
                            </div>
                            @error('scope_ids') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label">{{ __('costing_trans.notes') }}</label>
                        <textarea class="form-control" rows="2" wire:model="notes" placeholder="..."></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary" wire:click="next">
                        {{ __('costing_trans.next') }}
                    </button>
                </div>
            @endif

            {{-- STEP 2: تشغيل المحرك --}}
            @if($step === 2)
                <div class="alert alert-info d-flex align-items-center">
                    <i class="mdi mdi-information-outline me-2 fs-4"></i>
                    <div>{{ __('costing_trans.engine_hint') }}</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>{{ __('costing_trans.period') }}:</strong>
                                    <span class="ms-1">{{ $period_start }} — {{ $period_end }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>{{ __('costing_trans.scope') }}:</strong>
                                    <span class="ms-1">
                                        @if($scope_type==='hospital')
                                            {{ __('costing_trans.scope_hospital') }}
                                        @elseif($scope_type==='centers')
                                            {{ __('costing_trans.scope_centers') }}
                                            @if(!empty($scope_ids))
                                                <span class="text-muted">({{ implode(',', $scope_ids) }})</span>
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                @if($batch)
                                    <div class="mt-2">
                                        <span class="badge bg-secondary">{{ $batch->code }}</span>
                                        <span class="badge bg-outline-secondary text-muted ms-1">{{ $batch->status }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-start justify-content-end">
                        <div class="btn-group">
                            <button class="btn btn-light" wire:click="back">{{ __('costing_trans.back') }}</button>
                            <button class="btn btn-primary" wire:click="runEngine">
                                <i class="mdi mdi-cog-play-outline me-1"></i> {{ __('costing_trans.run_engine') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- STEP 3: مراجعة النتائج --}}
            @if($step === 3)
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted">{{ __('costing_trans.direct_count') }}</div>
                                <div class="fs-4 fw-bold">{{ $batch->direct_count ?? 0 }}</div>
                                <hr>
                                <div class="text-muted">{{ __('costing_trans.direct_total') }}</div>
                                <div class="fs-5">{{ number_format($batch->direct_total ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted">{{ __('costing_trans.overhead_count') }}</div>
                                <div class="fs-4 fw-bold">{{ $batch->overhead_count ?? 0 }}</div>
                                <hr>
                                <div class="text-muted">{{ __('costing_trans.overhead_total') }}</div>
                                <div class="fs-5">{{ number_format($batch->overhead_total ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted">{{ __('costing_trans.grand_total') }}</div>
                                <div class="fs-3 fw-bold">{{ number_format($batch->grand_total ?? 0, 2) }}</div>
                                <hr>
                                <div class="text-muted">{{ __('costing_trans.status') }}</div>
                                <span class="badge bg-info">{{ $batch->status ?? $status }}</span>
                                @if($batch?->code)
                                    <span class="badge bg-secondary ms-1">{{ $batch->code }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-light me-2" wire:click="back">{{ __('costing_trans.back') }}</button>
                        <button class="btn btn-success" wire:click="finalize">
                            <i class="mdi mdi-check-decagram-outline me-1"></i> {{ __('costing_trans.finalize') }}
                        </button>
                    </div>
                </div>
            @endif

            {{-- STEP 4: الإقفال والترحيل --}}
            @if($step === 4)
                <div class="alert alert-success d-flex align-items-center">
                    <i class="mdi mdi-check-circle-outline me-2 fs-4"></i>
                    <div>{{ __('costing_trans.finalized_ok') }}</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center">
                                <div>
                                    @if($batch?->code)
                                        <span class="badge bg-secondary me-2">{{ $batch->code }}</span>
                                    @endif
                                    <span class="badge bg-success">{{ __('costing_trans.completed') }}</span>
                                </div>
                                <div class="ms-3 text-muted">
                                    {{ $period_start }} — {{ $period_end }} /
                                    {{ $scope_type === 'hospital' ? __('costing_trans.scope_hospital') : __('costing_trans.scope_centers') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex justify-content-end align-items-start">
                        <div class="btn-group">
                            <button class="btn btn-light" wire:click="$set('step', 3)">{{ __('costing_trans.review') }}</button>
                            <button class="btn btn-primary" wire:click="postToGL">
                                <i class="mdi mdi-upload me-1"></i> {{ __('costing_trans.post_to_gl') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
