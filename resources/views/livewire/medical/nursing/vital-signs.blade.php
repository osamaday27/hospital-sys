{{-- resources/views/livewire/medical/nursing/vital-signs.blade.php --}}

<div>
    {{-- رسالة النجاح --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form @if($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        <div class="form-container">
            <div class="section-title">
                <i class="fas fa-heartbeat mr-2"></i>
                {{ $selectedRecord ? 'تعديل العلامات الحيوية' : 'العلامات الحيوية' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>درجة الحرارة (°C)</th>
                    <td>
                        <input type="text" wire:model.defer="temperature" class="form-control-custom" placeholder="سيلزية">
                        @error('temperature') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>معدل التنفس (/دقيقة)</th>
                    <td>
                        <input type="text" wire:model.defer="respiratory_rate" class="form-control-custom" placeholder="/دقيقة">
                        @error('respiratory_rate') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>ضغط الدم (systolic/diastolic)</th>
                    <td>
                        <input type="text" wire:model.defer="blood_pressure" class="form-control-custom" placeholder="مثال: 120/80" style="width:60%;">
                        @error('blood_pressure') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>النبض (/دقيقة)</th>
                    <td>
                        <input type="text" wire:model.defer="pulse" class="form-control-custom" placeholder="/دقيقة">
                        @error('pulse') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>السكر العشوائي (mg/dL)</th>
                    <td>
                        <input type="text" wire:model.defer="blood_sugar" class="form-control-custom" placeholder="ملجم/دل">
                        @error('blood_sugar') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>الوزن (كجم)</th>
                    <td>
                        <input type="text" wire:model.defer="weight" class="form-control-custom" placeholder="كجم">
                        @error('weight') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>الطول (سم)</th>
                    <td>
                        <input type="text" wire:model.defer="height" class="form-control-custom" placeholder="سم">
                        @error('height') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">{{ $selectedRecord ? 'تحديث' : 'حفظ' }}</button>
                @if($selectedRecord)
                    <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
                @endif
            </div>
        </div>
    </form>

    {{-- سجل الفحوصات السابقة --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل العلامات الحيوية</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>درجة الحرارة</th>
                        <th>التنفس</th>
                        <th>BP</th>
                        <th>النبض</th>
                        <th>سكر الدم</th>
                        <th>الوزن/الطول/BMI</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                        <tr>
                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($r->user)->name }}</td>
                            <td>{{ $r->temperature }}</td>
                            <td>{{ $r->respiratory_rate }}</td>
                            <td>{{ $r->systolic_bp }}/{{ $r->diastolic_bp }}</td>
                            <td>{{ $r->heart_rate }}</td>
                            <td>{{ $r->blood_sugar }}</td>
                            <td>{{ $r->weight }} كجم / {{ $r->height }} سم / {{ number_format($r->bmi,2) }}</td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $r->id }})" class="btn btn-sm btn-info">عرض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">لا توجد سجلات سابقة</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>
            
        </div>
    </div>
</div>
