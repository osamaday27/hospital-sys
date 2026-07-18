<div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-md me-2"></i> {{ __('surgical_trans.anesthesia_approval') }}</h5>
        <div>
            <button wire:click="$set('tab','form')" class="btn btn-light btn-sm {{ $tab === 'form' ? 'active' : '' }}">
                <i class="fas fa-edit"></i> {{ __('surgical_trans.new_approval') }}
            </button>
            <button wire:click="$set('tab','view')" class="btn btn-light btn-sm {{ $tab === 'view' ? 'active' : '' }}">
                <i class="fas fa-file-medical"></i> {{ __('surgical_trans.view_approval') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        {{-- ✅ رسالة الجلسة --}}
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- ✅ تبويب 1: نموذج الموافقة --}}
        @if ($tab === 'form')
            <form wire:submit.prevent="save" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">{{ __('surgical_trans.approval_date') }}</label>
                    <input type="date" class="form-control" wire:model="approval_date">
                    @error('approval_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('surgical_trans.notes') }}</label>
                    <textarea class="form-control" wire:model="notes"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('surgical_trans.approval_file') }}</label>
                    <input type="file" class="form-control" wire:model="approval_file">
                    @error('approval_file') <span class="text-danger">{{ $message }}</span> @enderror

                    @if ($approval_data && $approval_data->approval_file)
                        <div class="mt-2">
                            <a href="{{ asset($approval_data->approval_file) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                {{ __('surgical_trans.download_previous_file') }}
                            </a>
                        </div>
                    @endif
                </div>

                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save me-1"></i> {{ __('surgical_trans.save') }}
                </button>
            </form>
        @endif

        {{-- ✅ تبويب 2: عرض الموافقة --}}
        @if ($tab === 'view')
            @if ($approval_data)
                <div class="p-3 bg-light border rounded">
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i> {{ __('surgical_trans.approval_details') }}</h6>
                    <p><strong>{{ __('surgical_trans.doctor_name') }}:</strong> {{ $approval_data->doctor_name }}</p>
                    <p><strong>{{ __('surgical_trans.approval_date') }}:</strong> {{ $approval_data->approval_date }}</p>
                    <p><strong>{{ __('surgical_trans.notes') }}:</strong> {{ $approval_data->notes ?? '-' }}</p>

                    @if ($approval_data->approval_file)
                        <a href="{{ asset($approval_data->approval_file) }}" target="_blank"
                            class="btn btn-outline-primary mt-2">
                            <i class="fas fa-download me-1"></i> {{ __('surgical_trans.download_file') }}
                        </a>
                    @endif
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ __('surgical_trans.no_approval_yet') }}
                </div>
            @endif
        @endif
    </div>
</div>

{{-- ✅ Toast notification --}}
<script>
    document.addEventListener('livewire:load', function() {
        window.addEventListener('show-toast', event => {
            const { type, message } = event.detail;
            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3 show`;
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });
    });
</script>
