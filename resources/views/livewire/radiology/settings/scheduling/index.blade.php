<div>
    {{-- ✅ التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔹 Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'list' ? 'active' : '' }}" wire:click="$set('activeTab','list')">
                <i class="mdi mdi-table"></i> عرض الجداول
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $activeTab === 'form' ? 'active' : '' }}" wire:click="createNew">
                <i class="mdi mdi-plus-circle-outline"></i> إضافة / تعديل
            </button>
        </li>
    </ul>

    {{-- 🔹 جدول --}}
    @if($activeTab === 'list')
        <div class="card shadow-sm">
            <div class="card-body">
                <input type="text" class="form-control mb-3" placeholder="🔍 بحث..." wire:model="search">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>الغرفة</th>
                            <th>الجهاز</th>
                            <th>الفترة</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->room?->getTranslation('name','ar') }}</td>
                                <td>{{ $schedule->device?->getTranslation('name','ar') }}</td>
                                <td>{{ $schedule->shift_type }}</td>
                                <td>{{ $schedule->start_time }}</td>
                                <td>{{ $schedule->end_time }}</td>
                                <td>{{ $schedule->status === 'active' ? 'نشطة' : 'غير نشطة' }}</td>
                                <td>
                                    <button wire:click="edit({{ $schedule->id }})" class="btn btn-sm btn-primary">✏️ تعديل</button>
                                    <button onclick="confirmDelete({{ $schedule->id }})" class="btn btn-sm btn-danger">🗑️ حذف</button>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $schedules->links() }}
            </div>
        </div>
    @endif

    {{-- 🔹 نموذج --}}
    @if($activeTab === 'form')
        <div class="card shadow-sm">
            <div class="card-body">
                <form wire:submit.prevent="save" class="row g-3">
                    
                    {{-- الغرفة --}}
                    <div class="col-md-6">
                        <label class="form-label">الغرفة</label>
                        <select wire:model="room_id" class="form-select">
                            <option value="">اختر الغرفة</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->getTranslation('name','ar') }}</option>
                            @endforeach
                        </select>
                        @error('room_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- الجهاز --}}
                    <div class="col-md-6">
                        <label class="form-label">الجهاز</label>
                        <select wire:model="device_id" class="form-select">
                            <option value="">اختر الجهاز</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->getTranslation('name','ar') }}</option>
                            @endforeach
                        </select>
                        @error('device_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- نوع الفترة --}}
                    <div class="col-md-6">
                        <label class="form-label">نوع الفترة</label>
                        <select wire:model="shift_type" class="form-select">
                            <option value="">اختر الفترة</option>
                            <option value="morning">صباحية</option>
                            <option value="evening">مسائية</option>
                            <option value="night">ليلية</option>
                        </select>
                        @error('shift_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- وقت البداية --}}
                    <div class="col-md-3">
                        <label class="form-label">وقت البداية</label>
                        <input type="time" wire:model="start_time" class="form-control">
                        @error('start_time') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- وقت النهاية --}}
                    <div class="col-md-3">
                        <label class="form-label">وقت النهاية</label>
                        <input type="time" wire:model="end_time" class="form-control">
                        @error('end_time') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- عطلة / صيانة --}}
                    <div class="col-md-3 form-check mt-4">
                        <input type="checkbox" class="form-check-input" wire:model="is_holiday" id="holiday">
                        <label for="holiday" class="form-check-label">عطلة</label>
                    </div>
                    <div class="col-md-3 form-check mt-4">
                        <input type="checkbox" class="form-check-input" wire:model="is_maintenance" id="maintenance">
                        <label for="maintenance" class="form-check-label">صيانة</label>
                    </div>

                    {{-- الحالة --}}
                    <div class="col-md-6">
                        <label class="form-label">الحالة</label>
                        <select wire:model="status" class="form-select">
                            <option value="active">نشطة</option>
                            <option value="inactive">غير نشطة</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- زر الحفظ --}}
                    <div class="col-12">
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            💾 حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
{{-- ✅ SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: "هل أنت متأكد أنك تريد حذف هذا السجل؟",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#0d6efd',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.emit('deleteConfirmed', id);
            Swal.fire('تم الحذف!', 'تم حذف السجل بنجاح', 'success');
        }
    })
}
</script>

