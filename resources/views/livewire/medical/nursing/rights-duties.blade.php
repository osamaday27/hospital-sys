{{-- resources/views/livewire/medical/nursing/rights-assessment.blade.php --}}

<div>
    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form @if($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        <div class="form-container">
            <div class="section-title">
                <i class="fas fa-balance-scale mr-2"></i>
                {{ $selectedRecord ? 'تعديل الحقوق والواجبات' : 'الحقوق والواجبات' }}
            </div>

            <table class="form-table w-100">
                <tr>
                    <th>هل تم إخبارك بحقوقك وواجباتك؟</th>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="rights_yes" class="form-check-input"> نعم
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label mb-2">
                                    <input type="checkbox" wire:model.defer="rights_no" class="form-check-input"> لا
                                </label>
                                <input type="text" wire:model.defer="rights_action" class="form-control-custom" placeholder="وضح الإجراء المتخذ" style="width:60%; display:inline-block;">
                                @error('rights_action') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    {{ $selectedRecord ? 'تحديث' : 'حفظ' }}
                </button>
                @if($selectedRecord)
                    <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
                @endif
            </div>
        </div>
    </form>

    {{-- سجل السجلات السابقة --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل الحقوق والواجبات</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>الإجراء المتخذ</th>
                        <th class="text-center">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($record->user)->name }}</td>
                            <td>
                                {{ $record->rights_yes ? 'نعم' : 'لا' }}
                                @if(!$record->rights_yes && $record->rights_action)
                                    — {{ $record->rights_action }}
                                @endif
                            </td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $record->id }})" class="btn btn-sm btn-info">عرض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">لا توجد سجلات سابقة</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>
</div>
