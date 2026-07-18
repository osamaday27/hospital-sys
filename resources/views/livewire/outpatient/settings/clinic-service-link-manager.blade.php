<div>
    <div class="card border-0 shadow-lg rounded-4 bg-light bg-opacity-75 backdrop-blur">
        <div
            class="card-header bg-gradient bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary"><i class="mdi mdi-link-variant me-2"></i> إعدادات ربط الخدمات بالعيادات</h5>
            <button wire:click="refreshLinks" class="btn btn-light btn-sm shadow-sm">
                <i class="mdi mdi-refresh"></i> تحديث
            </button>
        </div>

        <div class="card-body">
            {{-- رسائل التنبيه --}}
            @if (session()->has('success'))
                <div class="alert alert-success shadow-sm rounded-pill px-4">
                    <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger shadow-sm rounded-pill px-4">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="save" class="row g-3 mb-4">
                <!-- العيادة -->
                <div class="col-md-4" wire:ignore>
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-hospital-building me-1"></i> اختر العيادة
                    </label>
                    <select class="form-select select2" wire:model="clinic_id">
                        <option value="">-- اختر العيادة --</option>
                        @foreach ($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name_ar }} - {{ $clinic->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- الخدمة -->
                <div class="col-md-4" wire:ignore>
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-stethoscope me-1 text-success"></i> اختر الخدمة
                    </label>
                    <select class="form-select select2" wire:model="service_id">
                        <option value="">-- اختر الخدمة --</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- النوع -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-format-list-bulleted-type me-1 text-info"></i> نوع الخدمة
                    </label>
                    <select class="form-select" wire:model="service_type">
                        <option value="examination">كشف</option>
                        <option value="follow_up">متابعة</option>
                        <option value="operation_follow_up">متابعة عملية</option>
                        <option value="Urgent_Consultation">كشف مستعجل</option>
                        <option value="External_Radiology_Report">تقرير طبي</option>
                        <option value="Medical_Report">عرض اشعة خارجية</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <!-- الحالة -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-toggle-switch me-1 text-warning"></i> الحالة
                    </label>
                    <select class="form-select" wire:model="status">
                        <option value="active">مفعل</option>
                        <option value="inactive">غير مفعل</option>
                    </select>
                </div>

                <!-- استشاري -->
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" wire:model="is_consultant" id="consultantCheck">
                        <label class="form-check-label fw-bold" for="consultantCheck">
                            <i class="mdi mdi-account-tie text-info"></i> استشاري
                        </label>
                    </div>
                </div>

                <!-- حالة طبيب -->
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" wire:model="is_doctor" id="doctorCheck">
                        <label class="form-check-label fw-bold" for="doctorCheck">
                            <i class="mdi mdi-doctor text-danger"></i> حالة طبيب
                        </label>
                    </div>
                </div>

                <!-- زر الحفظ -->
                <div class="col-12 text-end">
                    <button class="btn btn-gradient btn-success px-4 shadow-sm rounded-pill">
                        <i class="mdi mdi-content-save-outline me-1"></i> حفظ
                    </button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table id="linksTable" class="table table-striped table-hover align-middle shadow-sm rounded-3">
                    <thead class="table-info text-center">
                        <tr>
                            <th>#</th>
                            <th>العيادة</th>
                            <th>الخدمة</th>
                            <th>استشاري</th>
                            <th>حالة طبيب</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($links as $link)
                            <tr>
                                <td class="fw-bold">{{ $link->id }}</td>
                                <td>{{ $link->clinic->name_ar }}</td>
                                <td>{{ $link->service->name }}</td>
                                <td>{!! $link->is_consultant ? '<span class="badge bg-info">استشاري</span>' : '<span class="text-muted">-</span>' !!}</td>
                                <td>{!! $link->is_doctor ? '<span class="badge bg-primary">طبيب</span>' : '<span class="text-muted">-</span>' !!}</td>
                                <td>
                                    @switch($link->service_type)
                                        @case('examination')
                                            <span class="badge bg-secondary">كشف</span>
                                        @break

                                        @case('follow_up')
                                            <span class="badge bg-success">متابعة</span>
                                        @break

                                        @case('operation_follow_up')
                                            <span class="badge bg-warning text-dark">متابعة عملية</span>
                                        @break

                                        @case('Urgent_Consultation')
                                            <span class="badge bg-info">كشف مستعجل</span>
                                        @break

                                        @case('External_Radiology_Report')
                                            <span class="badge bg-warning text-dark">عرض اشعة خارجية</span>
                                        @break

                                        @case('Medical_Report')
                                            <span class="badge bg-warning text-dark">تقرير طبي</span>
                                        @break


                                    @break


                                @break


                            @break

                            @default
                                <span class="badge bg-dark">أخرى</span>
                        @endswitch
                    </td>
                    <td>
                        <span class="badge bg-{{ $link->status == 'active' ? 'success' : 'danger' }}">
                            {{ $link->status == 'active' ? 'مفعل' : 'غير مفعل' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <button onclick="confirmDelete({{ $link->id }})"
                            class="btn btn-sm btn-danger rounded-pill px-3">
                            <i class="mdi mdi-delete"></i> حذف
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>
</div>

<script>
    // تفعيل select2 مع Livewire
    document.addEventListener("livewire:load", () => {
        $('.select2').select2();
        $('.select2').on('change', function(e) {
            var data = $(this).val();
            var field = $(this).attr('wire:model');
            @this.set(field, data);
        });
    });

    // تأكيد الحذف
    function confirmDelete(id) {
        Swal.fire({
            title: "هل أنت متأكد؟",
            text: "لن تتمكن من التراجع بعد الحذف!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "نعم، احذف!",
            cancelButtonText: "إلغاء"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
                Swal.fire("تم الحذف!", "تم حذف السجل بنجاح.", "success");
            }
        });
    }
</script>
</div>
