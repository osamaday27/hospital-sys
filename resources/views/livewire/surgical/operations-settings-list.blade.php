{{-- resources/views/livewire/finance/operations-settings-list.blade.php --}}
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0" style="color: #fff;">
            <i class="fas fa-cog me-2"></i>{{ __('finance_trans.operations_settings') }}
        </h4>
        <div class="badge bg-white fs-6" style="color: black !important;">
            {{ __('finance_trans.total_services') }}: {{ $services->total() }}
        </div>
    </div>

    <div class="card-body">
        {{-- الفلاتر المحسنة --}}
        <div class="row mb-4 g-3">
            <div class="col-lg-4 col-md-6">
                <div class="form-floating">
                    <select wire:model="level1_id" class="form-select" id="level1Filter">
                        <option value="">{{ __('finance_trans.all_levels') }}</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                    <label for="level1Filter">{{ __('finance_trans.level1') }}</label>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="form-floating">
                    <select wire:model="sublevel_id" class="form-select" id="level2Filter">
                        <option value="">{{ __('finance_trans.all_sublevels') }}</option>
                        @foreach($sublevels as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                    <label for="level2Filter">{{ __('finance_trans.level2') }}</label>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="form-floating">
                    <input type="text" wire:model.debounce.500ms="search" class="form-control" 
                           id="searchInput" placeholder="{{ __('finance_trans.search_by_name') }}">
                    <label for="searchInput">{{ __('finance_trans.search') }}</label>
                </div>
            </div>
        </div>

        {{-- جدول الخدمات المحسن --}}
        <div class="table-responsive rounded-3 border">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="25%">{{ __('finance_trans.level1') }}</th>
                        <th width="25%">{{ __('finance_trans.level2') }}</th>
                        <th width="30%">{{ __('finance_trans.service_name') }}</th>
                        <th width="10%">{{ __('finance_trans.status') }}</th>
                        <th width="10%">{{ __('finance_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr class="{{ $service->pricelistServices->count() ? 'table-success-light' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary-light text-primary  me-2"style="border-radius: 26% !important;background-color: #1373c3 !important;">
                                        {{ $loop->iteration }}
                                    </span>
                                    {{ $service->sublevel->level1->name ?? '-' }}
                                </div>
                            </td>
                            <td>{{ $service->sublevel->name ?? '-' }}</td>
                            <td>
                                <strong>{{ $service->name }}</strong>
                                @if($service->description)
                                    <p class="text-muted small mb-0">{{ Str::limit($service->description, 50) }}</p>
                                @endif
                            </td>
                            <td>
                                @if($service->pricelistServices->count() > 0)
                                    <span class="badge bg-success rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ __('finance_trans.configured') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning rounded-pill text-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ __('finance_trans.pending') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('surgeryserviceitem', $service->id) }}" 
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                        data-bs-toggle="tooltip" 
                                        title="{{ __('finance_trans.edit_settings') }}">
                                        {{ __('finance_trans.edit_settings') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">{{ __('finance_trans.no_services_found') }}</h5>
                                    @if($level1_id || $sublevel_id || $search)
                                        <button wire:click="resetFilters" class="btn btn-sm btn-outline-primary mt-3">
                                            {{ __('finance_trans.reset_filters') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- التقسيم الصفحي --}}
        @if($services->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    {{ __('Showing') }} {{ $services->firstItem() }} - {{ $services->lastItem() }} 
                    {{ __('of') }} {{ $services->total() }} {{ __('results') }}
                </div>
                <div>
                    {{ $services->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .table-success-light {
        background-color: rgba(25, 135, 84, 0.05);
    }
    .badge.bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    .form-floating label {
        opacity: 0.8;
    }
    .card {
        border-radius: 0.75rem;
    }
</style>

<script>
    document.addEventListener('livewire:load', function() {
        // تهيئة أدوات التلميح
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // إعادة تهيئة الأدوات عند تحديث Livewire
        Livewire.hook('message.processed', () => {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    });
</script>
