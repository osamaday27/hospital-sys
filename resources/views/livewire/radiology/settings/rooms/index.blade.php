<div> {{-- ✅ رسائل التنبيه --}} @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3"> <i
                class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }} <button type="button"
                class="btn-close" data-bs-dismiss="alert"></button> </div>
        @endif {{-- 🔹 Tabs --}} <ul class="nav nav-tabs mb-3" id="roomTabs" role="tablist">
            <li class="nav-item" role="presentation"> <button class="nav-link {{ $activeTab === 'form' ? 'active' : '' }}"
                    id="form-tab" wire:click="$set('activeTab','form')" type="button" role="tab"> <i
                        class="mdi mdi-plus-circle-outline"></i> {{ __('radiology_rooms.form_tab') }} </button> </li>
            <li class="nav-item" role="presentation"> <button
                    class="nav-link {{ $activeTab === 'list' ? 'active' : '' }}" id="list-tab"
                    wire:click="$set('activeTab','list')" type="button" role="tab"> <i class="mdi mdi-table"></i>
                    {{ __('radiology_rooms.list_tab') }} </button> </li>
        </ul>
        <div class="tab-content" id="roomTabsContent"> {{-- 📌 تبويب النموذج (إنشاء/تعديل) --}} @if ($activeTab === 'form')
                <div class="card border-0 shadow-lg rounded-4 stylish-card mb-4">
                    <div class="card-header bg-gradient-success text-black fw-bold"> <i
                            class="mdi mdi-door-closed me-2"></i>
                        {{ $room_id ? __('radiology_rooms.edit_title') : __('radiology_rooms.create_title') }} </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save" class="row g-3"> {{-- اسم الغرفة (عربي) --}} <div class="col-md-4">
                                <label class="form-label">{{ __('radiology_rooms.name_ar') }}</label> <input
                                    type="text" class="form-control" wire:model.defer="name.ar"> @error('name.ar')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- اسم الغرفة (إنجليزي) --}} <div class="col-md-4"> <label
                                    class="form-label">{{ __('radiology_rooms.name_en') }}</label> <input type="text"
                                    class="form-control" wire:model.defer="name.en"> @error('name.en')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- كود الغرفة --}} <div class="col-md-4"> <label
                                    class="form-label">{{ __('radiology_rooms.code') }}</label> <input type="text"
                                    class="form-control" wire:model.defer="code"> @error('code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- الفرع --}} <div class="col-md-4"> <label
                                    class="form-label">{{ __('radiology_rooms.branch') }}</label> <select
                                    class="form-select" wire:model="branch_id">
                                    <option value="">{{ __('اختر') }}</option>
                                    @foreach ($branches as $br)
                                        <option value="{{ $br->id }}">{{ $br->name }}</option>
                                    @endforeach
                                </select> @error('branch_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- المبنى --}} <div class="col-md-4"> <label
                                    class="form-label">{{ __('radiology_rooms.building') }}</label> <select
                                    class="form-select" wire:model="building_id">
                                    <option value="">{{ __('اختر') }}</option>
                                    @foreach ($buildings as $b)
                                        <option value="{{ $b->id }}">
                                            {{ app()->getLocale() === 'ar' ? $b->name_ar : $b->name_en }} </option>
                                    @endforeach
                                </select> @error('building_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- الدور --}} <div class="col-md-4"> <label
                                    class="form-label">{{ __('radiology_rooms.floor') }}</label> <select
                                    class="form-select" wire:model="floor_id">
                                    <option value="">{{ __('اختر') }}</option>
                                    @foreach ($floors as $f)
                                        <option value="{{ $f->id }}">
                                            {{ app()->getLocale() === 'ar' ? $f->name_ar : $f->name_en }} </option>
                                    @endforeach
                                </select> @error('floor_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- السعة --}} <div class="col-md-3"> <label
                                    class="form-label">{{ __('radiology_rooms.capacity_per_hour') }}</label> <input
                                    type="number" class="form-control" wire:model.defer="capacity_per_hour">
                                @error('capacity_per_hour')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> {{-- الحالة --}} <div class="col-md-3"> <label
                                    class="form-label">{{ __('radiology_rooms.status') }}</label> <select
                                    class="form-select" wire:model.defer="status">
                                    <option value="active">{{ __('radiology_rooms.active') }}</option>
                                    <option value="inactive">{{ __('radiology_rooms.inactive') }}</option>
                                </select> </div> {{-- أزرار --}} <div class="col-12 d-flex gap-2"> <button
                                    class="btn btn-success rounded-pill px-4 shadow-sm"> <i
                                        class="mdi mdi-content-save"></i>
                                    {{ $room_id ? __('radiology_rooms.update') : __('radiology_rooms.save') }}
                                </button> <button type="button" wire:click="resetInput"
                                    class="btn btn-primary rounded-pill px-4 shadow-sm"> <i
                                        class="mdi mdi-plus-circle-outline"></i> {{ __('radiology_rooms.create') }}
                                </button> </div>
                        </form>
                    </div>
                </div> @endif {{-- 📌 تبويب عرض الغرف --}} @if ($activeTab === 'list')
                    <div class="card border-0 shadow-lg rounded-4 stylish-card">
                        <div
                            class="card-header bg-gradient-primary text-black fw-bold d-flex justify-content-between align-items-center">
                            <span><i class="mdi mdi-table"></i> {{ __('radiology_rooms.list') }}</span> <input
                                type="text" class="form-control w-25" placeholder="🔍 بحث..."
                                wire:model.debounce.500ms="search"> </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>{{ __('radiology_rooms.code') }}</th>
                                        <th>{{ __('radiology_rooms.name') }}</th>
                                        <th>{{ __('radiology_rooms.building') }}</th>
                                        <th>{{ __('radiology_rooms.floor') }}</th>
                                        <th>{{ __('radiology_rooms.branch') }}</th>
                                        <th>{{ __('radiology_rooms.capacity_per_hour') }}</th>
                                        <th>{{ __('radiology_rooms.status') }}</th>
                                        <th>{{ __('radiology_rooms.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rooms as $room)
                                        <tr class="text-center">
                                            <td>{{ $room->id }}</td>
                                            <td>{{ $room->code }}</td>
                                            <td>{{ $room->getTranslation('name', app()->getLocale()) }}</td>
                                            <td>{{ app()->getLocale() === 'ar' ? $room->building?->name_ar : $room->building?->name_en }}
                                            </td>
                                            <td>{{ app()->getLocale() === 'ar' ? $room->floor?->name_ar : $room->floor?->name_en }}
                                            </td>
                                            <td>{{ $room->branch?->name }}</td>
                                            <td>{{ $room->capacity_per_hour }}</td>
                                            <td>
                                                @if ($room->status == 'active')
                                                    <span
                                                        class="badge bg-success">{{ __('radiology_rooms.active') }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger">{{ __('radiology_rooms.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td> <button wire:click="edit({{ $room->id }})"
                                                    class="btn btn-sm btn-warning me-1"> <i
                                                        class="mdi mdi-pencil"></i> </button> <button
                                                    onclick="confirmDelete({{ $room->id }})"
                                                    class="btn btn-sm btn-danger"> <i class="mdi mdi-delete"></i>
                                                </button> </td>
                                    </tr> @empty <tr>
                                            <td colspan="9" class="text-center text-muted py-3">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer"> {{ $rooms->links() }} </div>
                    </div>
                @endif
        </div>
</div> {{-- ✅ SweetAlert2 --}}
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'تحذير',
            text: '⚠️ هل أنت متأكد أنك تريد حذف هذا السجل؟ لا يمكن التراجع!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('delete', id);
                Swal.fire('تم الحذف!', '✅ تم حذف السجل بنجاح.', 'success');
            }
        })
    }
</script>
