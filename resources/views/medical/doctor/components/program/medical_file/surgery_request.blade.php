<div class="tab-pane fade" id="surgery_request" role="tabpanel">
    <div class="row">
        <!-- قسم إضافة طلب جراحة -->
        <div class="col-md-6">
            <div class="card border-primary shadow-lg rounded">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="mdi mdi-plus-circle me-2"></i>
                    <span>{{ trans('doctor_trans.add_surgery_request') }}</span>
                </div>
                <div class="card-body">
                    <form id="surgeryForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input id="id_patient" name="id_patient" value="{{ $patient->id }}" type="hidden" />
                            <label class="form-label">نوع العملية</label>
                            <select class="js-example-basic-single" name="surgical_id">
                                @foreach ($services as $date)
                                    <option value="{{ $date->id }}">{{ $date->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">التاريخ</label>
                            <input type="date" name="booking_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">حجز العملية</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- قسم عرض الحجوزات السابقة -->
        <div class="col-md-6">
            <div class="card border-secondary shadow-lg rounded">
                <div class="card-header bg-secondary text-white d-flex align-items-center">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    <span>{{ trans('doctor_trans.previous_examinations') }}</span>
                </div>
                <div class="card-body">
                    <table id="surgery_requestTable" name="surgery_requestTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>العملية</th>
                                <th>التاريخ</th>
                                <th>الملاحظات</th>
                                <th>تم الطلب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- سيتم ملء الجدول تلقائيًا عبر DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تهيئة DataTable لعرض الحجوزات السابقة
        let table = $('#surgery_requestTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            order: [
                [1, 'desc']
            ],
            language: {
                search: "🔍 بحث:",
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
            ajax: "{{ route('surgery_requests.index', $patient->id) }}",
            columns: [{
                    data: 'services',
                    name: 'services'
                },
                {
                    data: 'booking_date',
                    name: 'booking_date'
                },
                {
                    data: 'notes',
                    name: 'notes'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'notes',
                    name: 'notes'
                }
            
            ]
        });

        // معالجة الطلب عند إرسال النموذج
        $('#surgeryForm').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('surgery_requests.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحجز بنجاح',
                        text: response.message,
                    });
                    $('#surgeryForm')[0].reset();
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
