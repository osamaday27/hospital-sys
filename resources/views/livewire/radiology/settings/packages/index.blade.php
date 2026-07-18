<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- البحث --}}
    <div class="mb-4">
        <input type="text" wire:model="search" 
               class="form-control rounded-pill shadow-sm px-3"
               placeholder="{{ __('radiology_trans.packages_search') }}">
    </div>

    {{-- النموذج --}}
    <div class="card border-0 shadow-lg rounded-4 stylish-card mb-4">
        <div class="card-header bg-gradient-primary text-black fw-bold">
            <i class="mdi mdi-package-variant me-2"></i> {{ __('radiology_trans.packages_title') }}
        </div>
        <div class="card-body p-4">
            <form wire:submit.prevent="save" class="row g-4">

                {{-- الاسم بالعربية --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_name_ar') }}</label>
                    <input type="text" class="form-control" wire:model="name.ar">
                    @if ($name['ar'])
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-eye"></i> {{ $name['ar'] }}
                        </div>
                    @endif
                    @error('name.ar') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- الاسم بالإنجليزية --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_name_en') }}</label>
                    <input type="text" class="form-control" wire:model="name.en">
                    @if ($name['en'])
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-eye"></i> {{ $name['en'] }}
                        </div>
                    @endif
                    @error('name.en') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- الوصف --}}
                <div class="col-md-12">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_desc_ar') }}</label>
                    <textarea class="form-control" wire:model="description.ar"></textarea>
                    @if ($description['ar'])
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-eye"></i> {{ $description['ar'] }}
                        </div>
                    @endif
                </div>

                {{-- تاريخ البداية --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_start_date') }}</label>
                    <input type="date" class="form-control" wire:model="start_date">
                    @if ($start_date)
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-calendar"></i> {{ $start_date }}
                        </div>
                    @endif
                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- تاريخ النهاية --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_end_date') }}</label>
                    <input type="date" class="form-control" wire:model="end_date">
                    @if ($end_date)
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-calendar"></i> {{ $end_date }}
                        </div>
                    @endif
                    @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- العمر --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_age_from') }}</label>
                    <input type="number" class="form-control" wire:model="age_from">
                    @if ($age_from)
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-eye"></i> {{ $age_from }}
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_age_to') }}</label>
                    <input type="number" class="form-control" wire:model="age_to">
                    @if ($age_to)
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-eye"></i> {{ $age_to }}
                        </div>
                    @endif
                </div>

                {{-- الجنس --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_gender') }}</label>
                    <select class="form-select" wire:model="gender">
                        <option value="both">{{ __('radiology_trans.gender_both') }}</option>
                        <option value="male">{{ __('radiology_trans.gender_male') }}</option>
                        <option value="female">{{ __('radiology_trans.gender_female') }}</option>
                    </select>
                    @if ($gender)
                        <div class="preview-box mt-2 p-2 bg-light rounded shadow-sm">
                            <i class="mdi mdi-eye"></i> {{ __('radiology_trans.' . 'gender_' . $gender) }}
                        </div>
                    @endif
                </div>

                {{-- الحالة --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('radiology_trans.packages_status') }}</label>
                    <select class="form-select" wire:model="status">
                        <option value="active">{{ __('radiology_trans.status_active') }}</option>
                        <option value="inactive">{{ __('radiology_trans.status_inactive') }}</option>
                    </select>
                </div>

                {{-- أزرار الحفظ والإلغاء --}}
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm me-2">
                        <i class="mdi mdi-content-save-outline"></i> {{ __('radiology_trans.btn_save') }}
                    </button>
                    <button type="button" wire:click="resetInput" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-cancel"></i> {{ __('radiology_trans.btn_cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 📋 الجدول --}}
    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('radiology_trans.packages_name_ar') }}</th>
                        <th>{{ __('radiology_trans.packages_start_date') }} - {{ __('radiology_trans.packages_end_date') }}</th>
                        <th>{{ __('radiology_trans.packages_age_from') }} - {{ __('radiology_trans.packages_age_to') }}</th>
                        <th>{{ __('radiology_trans.packages_gender') }}</th>
                        <th>{{ __('radiology_trans.packages_status') }}</th>
                        <th>{{ __('radiology_trans.btn_edit') }}/{{ __('radiology_trans.btn_delete') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                        <tr>
                            <td>{{ $package->id }}</td>
                            <td>{{ $package->getTranslation('name', app()->getLocale()) }}</td>
                            <td>{{ $package->start_date }} - {{ $package->end_date }}</td>
                            <td>{{ $package->age_from }} - {{ $package->age_to }}</td>
                            <td>{{ __('radiology_trans.gender_' . $package->gender) }}</td>
                            <td>
                                @if($package->status == 'active')
                                    <span class="badge bg-success">{{ __('radiology_trans.status_active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('radiology_trans.status_inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button wire:click="edit({{ $package->id }})" class="btn btn-primary btn-sm rounded-pill shadow-sm me-1">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button onclick="confirmDelete({{ $package->id }})" class="btn btn-danger btn-sm rounded-pill shadow-sm">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('radiology_trans.packages_no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $packages->links() }}
        </div>
    </div>
</div>

{{-- ✅ SweetAlert2 --}}
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '{{ __("radiology_trans.alert_confirm_title") }}',
            text: '{{ __("radiology_trans.alert_confirm_text") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: '{{ __("radiology_trans.alert_confirm_yes") }}',
            cancelButtonText: '{{ __("radiology_trans.alert_confirm_cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('deleteConfirmed', id);
                Swal.fire('{{ __("radiology_trans.alert_deleted") }}', '{{ __("radiology_trans.alert_deleted_text") }}', 'success');
            }
        })
    }
</script>
