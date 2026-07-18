@extends('layouts.master_table')

@section('title')
    {{ trans('contract_settings.edit_second_level_contract') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('contract_settings.edit_second_level_contract') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('management_contract_second_level.index') }}">
                                {{ trans('contract_settings.contracts') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('contract_settings.edit_second_level_contract') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->
    <div id="alert-container"></div> <!-- مكان الرسائل -->

    <!-- Start Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('management_contract_second_level.update', $contract_details->id ?? '') }}"
                        method="POST" id="update-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" value="{{ $second_level_id }}" name="second_level_id" />
                        <div class="row">
                            <!-- Contract Details Section -->
                            <h5 class="mb-3">{{ trans('contract_settings.general_details') }}</h5>

                            <!-- Discount & Contribution Fields -->
                            <h5 class="mt-4 mb-3">{{ trans('contract_settings.discount_contribution') }}</h5>
                            @php
                                $services = [
                                    'all_services',
                                    'clinic_services',
                                    'inpatient_services',
                                    'surgical_services',
                                    'lab_services',
                                    'emergency_services',
                                    'rad_services',
                                    'medications',
                                    'supplies',
                                ];
                            @endphp

                            @foreach ($services as $service)
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="discount_percentage_{{ $service }}"
                                            class="form-label">{{ trans('contract_settings.discount_percentage_' . $service) }}</label>
                                        <input type="number" step="0.01" name="discount_percentage_{{ $service }}"
                                            id="discount_percentage_{{ $service }}" class="form-control"
                                            value="{{ old('discount_percentage_' . $service, $contract_details->{'discount_percentage_' . $service} ?? '') }}">
                                        @error('discount_percentage_' . $service)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="patient_contribution_{{ $service }}"
                                            class="form-label">{{ trans('contract_settings.patient_contribution_' . $service) }}</label>
                                        <input type="number" step="0.01"
                                            name="patient_contribution_{{ $service }}"
                                            id="patient_contribution_{{ $service }}" class="form-control"
                                            value="{{ old('patient_contribution_' . $service, $contract_details->{'patient_contribution_' . $service} ?? '') }}">
                                        @error('patient_contribution_' . $service)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                    </form>
                    <!-- Additional Services -->
                    <h5 class="mt-4 mb-3">{{ trans('contract_settings.additional_services') }}</h5>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="search" class="form-control" id="search-level"
                                        placeholder="{{ trans('datatables.search') }}" style="width: 250px;">
                                </div>
                                <div class="hstack gap-2">
                                    <!-- قسم إعدادات عرض الصفوف (lengthMenu) منفصل عن الأزرار الأخرى -->

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table id="level3-table" class="table dt-responsive nowrap align-middle"
                            style="width: 100%; border: aliceblue;">
                            <thead>
                                <tr>
                                    <th>{{ trans('pricing_list.id') }}</th>
                                    <th>{{ trans('pricing_list.hospital_code') }}</th>
                                    <th>{{ trans('pricing_list.service_name') }}</th>
                                    <th>{{ trans('pricing_list.sublevel') }}</th>
                                    <th>{{ trans('contract_settings.services_not_covered') }}</th>
                                    <th>{{ trans('contract_settings.services_require_approval') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('management_contract_second_level.index') }}"
                        class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        $(function() {
            var table = $('#level3-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('services/list') }}",
                    data: function(d) {
                        // استخراج الـ contract_id من عنوان URL
                        const urlParts = window.location.pathname.split('/');
                        const encryptedId = urlParts[urlParts.length - 2];
                        d.contract_id = encryptedId;

                        // إضافة فلاتر بحث إضافية (اختياري)
                        d.name = $('#search-name').val(); // فرضًا هناك حقل بحث بالـ ID "search-name"
                        d.status = $('#search-status')
                            .val(); // فرضًا هناك حقل بحث بالـ ID "search-status"
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'hospital_code',
                        name: 'hospital_code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'level_type',
                        name: 'level_type'
                    },
                    {
                        data: 'services_not_covered',
                        name: 'services_not_covered',
                        orderable: false
                    },
                    {
                        data: 'services_require_approval',
                        name: 'services_require_approval',
                        orderable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        title: 'Service Data',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'Service Data',
                        className: 'btn btn-outline-success'
                    },
                    {
                        extend: 'pdf',
                        title: 'Service Data',
                        className: 'btn btn-outline-danger'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary'
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                    info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                    processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
                    paginate: {
                        "previous": "{{ __('datatables.previous') }}",
                        "next": "{{ __('datatables.next') }}"
                    }
                }
            });
            // البحث حسب الحقول المختلفة
            $('#search-name').on('input', function() {
                table.draw(); // إعادة تحميل الجدول مع الفلتر الجديد
            });
            $('#search-status').on('change', function() {
                table.draw(); // إعادة تحميل الجدول مع الفلتر الجديد
            });
        });
    </script>
    <script>
        $(function() {
            // تعريف المتغيرات
            var servicesRequiringApproval = [];
            var servicesNotCovered = [];
            // عند تغيير قيمة في الـ input أو الـ checkbox
            $('#update-form input, #update-form select').on('change', function() {
                saveFormData();
            });

            // عند تغيير حالة الـ checkbox للخدمات
            $(document).on('change', '.checkboxservicec', function() {
                var value = $(this).val();
                var field = $(this).data('field');
                var serviceId = $(this).data('service-id');
                var serviceName = $(this).data('service-name');
                var isChecked = $(this).prop('checked');

                // تحديث الـ array الخاصة بالخدمات التي تحتاج الموافقة أو غير مغطاة
                updateServiceLists(field, serviceId, isChecked);
                // حفظ البيانات
                saveFormData();
            });
            // دالة لتحديث قائمة الخدمات التي تحتاج موافقة أو غير مغطاة
            function updateServiceLists(field, serviceId, isChecked) {
                if (field === 'services_require_approval' && isChecked) {
                    servicesRequiringApproval.push(serviceId);
                } else if (field === 'services_not_covered' && isChecked) {
                    servicesNotCovered.push(serviceId);
                } 
                else {
                    // إزالة العنصر إذا تم إلغاء التحديد
                    if (field === 'services_require_approval') {
                        servicesRequiringApproval = servicesRequiringApproval.filter(id => id !== serviceId);
                    }
                    if (field === 'services_not_covered') {
                        servicesNotCovered = servicesNotCovered.filter(id => id !== serviceId);
                    }
                }
            }
            // دالة لحفظ البيانات
            function saveFormData() {
                var formData = $('#update-form').serializeArray(); // الحصول على جميع القيم في الفورم

                // إضافة البيانات المتعلقة بالخدمات التي تحتاج الموافقة أو غير مغطاة
                formData.push({
                    name: 'services_require_approval',
                    value: JSON.stringify(servicesRequiringApproval)
                });
                formData.push({
                    name: 'services_not_covered',
                    value: JSON.stringify(servicesNotCovered)
                });
                // إرسال البيانات عبر AJAX
                $.ajax({
                    url: "{{ route('management_contract_second_level.storeOrUpdate') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                            //table.ajax.reload(); // إعادة تحميل الجدول لعرض التغييرات
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('error', "There was an error updating the price.");
                    }
                });
            }


        });
        // دالة لعرض الرسائل بشكل مخصص
        function showAlert(type, message) {
            // تحديد نوع التنبيه
            var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';

            // إنشاء رسالة التنبيه
            var alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show shadow" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="ri-${type === 'success' ? 'check-line' : 'alert-line'} align-middle"></i>
                        </div>
                        <div class="flex-grow-1">
                            ${message}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                `;

            // إضافة الرسالة إلى الحاوية
            $('#alert-container').append(alertHtml);

            // إخفاء الرسالة تلقائيًا بعد 15 ثانية
            setTimeout(function() {
                $('#alert-container .alert').first().alert('close');
            }, 5000);
        }
    </script>
@endsection
