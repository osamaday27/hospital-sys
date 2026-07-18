<div wire:ignore.self class="modal fade" id="medicalTeamModal" tabindex="-1" aria-labelledby="medicalTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="medicalTeamModalLabel">تعديل الفريق الطبي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-check-line me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <h6>أعضاء الفريق الحاليين:</h6>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الوظيفة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teamMembers as $index => $member)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $member->medical_team->name ?? '-' }}</td>
                                <td>{{ $member->role }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" wire:click="removeTeamMember({{ $member->id }})">
                                        <i class="ri-delete-bin-line"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">لا يوجد أعضاء حاليًا</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <hr>

                <h6>إضافة عضو جديد:</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>اختر الموظف</label>
                        <select class="form-select" wire:model="newMemberId">
                            <option value="">اختر موظفًا</option>
                            @foreach ($availableEmployees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('newMemberId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>الدور في الفريق</label>
                        <input type="text" class="form-control" wire:model="newRole" placeholder="مثال: جراح، ممرض...">
                        @error('newRole') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button class="btn btn-primary" wire:click="addTeamMember">
                        <i class="ri-add-line"></i> إضافة العضو
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('showMedicalTeamModal', () => {
        let modal = new bootstrap.Modal(document.getElementById('medicalTeamModal'));
        modal.show();
    });
</script>
@endpush
