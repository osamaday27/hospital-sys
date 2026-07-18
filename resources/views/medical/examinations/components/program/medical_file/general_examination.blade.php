<div class="tab-pane fade" id="general_examination" role="tabpanel">
    <div class="row">
        <!-- Section for Adding Data -->
        <div class="col-md-6">
            <div class="card border-primary shadow-lg rounded">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="mdi mdi-plus-circle me-2"></i>
                    <span>{{ trans('doctor_trans.add_general_examination') }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ trans('doctor_trans.general_appearance') }}</label>
                        <input type="text" class="form-control" name="general_appearance"
                            placeholder="{{ trans('doctor_trans.enter_general_appearance') }}">
                    </div>
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{ trans('doctor_trans.system') }}</th>
                                <th>Insignificant</th>
                                <th>{{ trans('doctor_trans.comments') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="mdi mdi-human-head text-info me-1"></i>
                                    {{ trans('doctor_trans.head_neck') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckHeadNeck" name="SwitchCheckHeadNeck" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_head_neck"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-lungs text-danger me-1"></i> {{ trans('doctor_trans.chest') }}
                                </td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckChest" name="SwitchCheckChest" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_chest"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-stomach text-warning me-1"></i>
                                    {{ trans('doctor_trans.abdomen') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckAbdomen" name="SwitchCheckAbdomen" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_abdomen"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-brain text-primary me-1"></i> {{ trans('doctor_trans.cns') }}
                                </td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckCNS" name="SwitchCheckCNS" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_cns"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-heart-pulse text-danger me-1"></i>
                                    {{ trans('doctor_trans.heart') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckHeart" name="SwitchCheckHeart" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_heart"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-food-apple text-success me-1"></i>
                                    {{ trans('doctor_trans.git') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckGIT" name="SwitchCheckGIT" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_git"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-water text-primary me-1"></i> {{ trans('doctor_trans.edema') }}
                                </td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckEdema" name="SwitchCheckEdema" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_edema"></td>

                            </tr>
                            <tr>
                                <td><i class="mdi mdi-water text-primary me-1"></i> {{ trans('doctor_trans.other') }}
                                </td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="SwitchCheckOther" name="SwitchCheckOther" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_other"></td>

                            </tr>
                        </tbody>
                    </table>
                    <button id="saveGeneralExamination" class="btn btn-success w-100 mt-3 fw-bold">
                        <i class="mdi mdi-content-save me-1"></i> {{ trans('doctor_trans.save') }}
                    </button>

                </div>
            </div>
        </div>
        <!-- Section for Displaying Data -->
        <div class="col-md-6">
            <div class="card border-secondary shadow-lg rounded">
                <div class="card-header bg-secondary text-white d-flex align-items-center">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    <span>{{ trans('doctor_trans.previous_examinations') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive"> <!-- هنا لضبط استجابة الجدول -->
                        <table class="table table-striped table-bordered dt-responsive nowrap examination-table"
                            id="general_examinationsTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ trans('doctor_trans.system') }}</th>
                                    <th>{{ trans('doctor_trans.date') }}</th>
                                    <th>{{ trans('doctor_trans.createby') }}</th>
                                </tr>
                            </thead>
                            <tbody id="previous-examinations-body">
                                <!-- Dynamic Data Will be Loaded Here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#saveGeneralExamination').on('click', function(e) {
            e.preventDefault();

            let formData = {
                patient_id: $('input[name="id_patient"]').val(),
                visit_id: $('input[name="id_visit"]').val(),
                general_appearance: $('input[name="general_appearance"]').val(),
                head_neck: $('#SwitchCheckHeadNeck').prop('checked') ? 1 : 0,
                comment_head_neck: $('input[name="comment_head_neck"]').val(),
                chest: $('#SwitchCheckChest').prop('checked') ? 1 : 0,
                comment_chest: $('input[name="comment_chest"]').val(),
                abdomen: $('#SwitchCheckAbdomen').prop('checked') ? 1 : 0,
                comment_abdomen: $('input[name="comment_abdomen"]').val(),
                cns: $('#SwitchCheckCNS').prop('checked') ? 1 : 0,
                comment_cns: $('input[name="comment_cns"]').val(),
                heart: $('#SwitchCheckHeart').prop('checked') ? 1 : 0,
                comment_heart: $('input[name="comment_heart"]').val(),
                git: $('#SwitchCheckGIT').prop('checked') ? 1 : 0,
                comment_git: $('input[name="comment_git"]').val(),
                edema: $('#SwitchCheckEdema').prop('checked') ? 1 : 0,
                comment_edema: $('input[name="comment_edema"]').val(),
                other: $('#SwitchCheckOther').prop('checked') ? 1 : 0,
                comment_other: $('input[name="comment_other"]').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: "{{ route('general_examinations.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ',
                        text: response.message,
                    });
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الإدخال',
                        text: errorMessage,
                    });
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // تهيئة DataTable
        let table = $('#general_examinationsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true, // اجعل الجدول متجاوبًا
            autoWidth: false, // تعطيل العرض التلقائي للأعمدة
            order: [
                [0, 'desc']
            ], // ترتيب افتراضي حسب تاريخ الإضافة (آخر البيانات أولًا)
            language: {
                search: "🔍 بحث:", // تخصيص نص البحث
                lengthMenu: "عرض _MENU_ سجل لكل صفحة",
                info: "عرض _START_ إلى _END_ من _TOTAL_ سجل",
                infoEmpty: "لا توجد بيانات متاحة",
                infoFiltered: "(تمت تصفيتها من إجمالي _MAX_ سجلات)",
                paginate: {
                    first: "الأول",
                    last: "الأخير",
                    next: "التالي",
                    previous: "السابق"
                }
            },
            ajax: "{{ route('general_examinations.index', $patient->id) }}",
            columns: [
            { data: 'systems_review', name: 'systems_review' },
            { data: 'formatted_created_at', name: 'formatted_created_at' },
            { data: 'user_name', name: 'user_name' }
        ]
        });
    });
</script>
