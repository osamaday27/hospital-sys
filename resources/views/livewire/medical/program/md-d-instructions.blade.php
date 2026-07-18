<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- نموذج الإضافة --}}
    <div class="card mb-4">
        <div class="card-header">إضافة تعليمات</div>
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>معرّف المريض</label>
                        <input type="number" wire:model.defer="patient_id" class="form-control">
                        @error('patient_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>معرّف الزيارة</label>
                        <input type="number" wire:model.defer="visit_id" class="form-control">
                        @error('visit_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>معرّف الحجز</label>
                        <input type="number" wire:model.defer="reservation_id" class="form-control">
                        @error('reservation_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>درجة الأهمية</label>
                        <select wire:model.defer="importance" class="form-select">
                            <option value="low">منخفض</option>
                            <option value="medium">متوسط</option>
                            <option value="high">عالي</option>
                        </select>
                        @error('importance') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12">
                        <label>نصّ التعليمة</label>
                        <textarea wire:model.defer="instruction" rows="3" class="form-control"></textarea>
                        @error('instruction') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-2">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" wire:model="time_specific" id="time_specific">
                            <label class="form-check-label" for="time_specific">
                                موعد محدد
                            </label>
                        </div>
                    </div>
                    @if($time_specific)
                        <div class="col-md-4">
                            <label>التاريخ & الوقت</label>
                            <input type="datetime-local" wire:model.defer="scheduled_at" class="form-control">
                            @error('scheduled_at') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="col-md-2">
                        <label>عدد التكرار</label>
                        <input type="number" wire:model.defer="repeat_count" class="form-control" min="1">
                        @error('repeat_count') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>الفترة بين التكرار</label>
                        <input type="text" wire:model.defer="repeat_interval" class="form-control" placeholder="مثلاً 4 hours">
                        @error('repeat_interval') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label>عند التكرار</label>
                        <select wire:model.defer="on_repeat_action" class="form-select">
                            <option value="duplicate">نسخ التعليمة</option>
                            <option value="remind">تنبيه فقط</option>
                            <option value="skip">تخطي</option>
                        </select>
                        @error('on_repeat_action') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- قائمة التعليمات --}}
    <div class="card">
        <div class="card-header">قائمة التعليمات</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مريض</th>
                        <th>تعليمة</th>
                        <th>أهمية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instructions as $ins)
                        <tr>
                            <td>{{ $ins->id }}</td>
                            <td>{{ $ins->patient->full_name ?? $ins->patient_id }}</td>
                            <td>{{ Str::limit($ins->instruction, 50) }}</td>
                            <td>{{ $ins->importance }}</td>
                            <td>{{ $ins->status }}</td>
                            <td class="d-flex gap-1">
                                @if($ins->status === 'pending')
                                    <button wire:click="markInProgress({{ $ins->id }})" class="btn btn-sm btn-info">ابدأ</button>
                                @endif
                                @if(in_array($ins->status, ['pending','in_progress']))
                                    <button wire:click="markFinished({{ $ins->id }})" class="btn btn-sm btn-success">تمت</button>
                                @endif
                                <button wire:click="openNotes({{ $ins->id }})" class="btn btn-sm btn-secondary">ملاحظات</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $instructions->links() }}
        </div>
    </div>

    {{-- موديال الملاحظات --}}
    <div class="modal fade" id="notesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="saveNotes">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ملاحظات التعليمة #{{ $selectedInstruction->id ?? '' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <textarea wire:model.defer="notes" class="form-control" rows="4"></textarea>
                        @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('show-notes-modal', () => {
            new bootstrap.Modal(document.getElementById('notesModal')).show();
        });
        window.addEventListener('hide-notes-modal', () => {
            bootstrap.Modal.getInstance(document.getElementById('notesModal')).hide();
        });
    </script>
</div>
