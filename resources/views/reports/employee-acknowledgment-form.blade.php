@extends('layouts.app')

@section('title', 'إقرار الاطلاع على دليل السياسات والإجراءات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إقرار الاطلاع على دليل السياسات والإجراءات</h3>
                </div>
                <div class="card-body">
                    <form id="acknowledgmentForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_id">اختر الموظف:</label>
                                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                                        <option value="">-- اختر الموظف --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                    data-name="{{ $employee->name }}"
                                                    data-code="{{ $employee->employee_code }}"
                                                    data-job="{{ $employee->job->name_ar ?? '' }}"
                                                    data-department="{{ $employee->department->name_ar ?? '' }}">
                                                {{ $employee->name }} - {{ $employee->employee_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format">نوع الملف:</label>
                                    <select name="format" id="format" class="form-control">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                        <option value="preview">معاينة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" onclick="generateReport()">
                                        <i class="fas fa-file-pdf"></i> إنشاء التقرير
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="previewReport()">
                                        <i class="fas fa-eye"></i> معاينة
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="selectMultiple()">
                                        <i class="fas fa-users"></i> إنشاء متعدد
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Multiple Selection Section -->
                    <div id="multipleSelection" style="display: none;">
                        <hr>
                        <h5>اختيار متعدد للموظفين</h5>
                        <form id="bulkForm">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>اختر الموظفين:</label>
                                        <select name="employee_ids[]" id="employee_ids" class="form-control select2" multiple>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">
                                                    {{ $employee->name }} - {{ $employee->employee_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success" onclick="generateBulkReport()">
                                        <i class="fas fa-download"></i> تحميل مضغوط
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Employee Preview -->
                    <div id="employeePreview" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5>معاينة بيانات الموظف</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>الاسم:</strong>
                                        <span id="previewName">-</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>الكود:</strong>
                                        <span id="previewCode">-</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>الوظيفة:</strong>
                                        <span id="previewJob">-</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>القسم:</strong>
                                        <span id="previewDepartment">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: 'اختر الموظف',
        allowClear: true
    });

    // Update preview when employee is selected
    $('#employee_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#previewName').text(selectedOption.data('name'));
            $('#previewCode').text(selectedOption.data('code'));
            $('#previewJob').text(selectedOption.data('job'));
            $('#previewDepartment').text(selectedOption.data('department'));
            $('#employeePreview').show();
        } else {
            $('#employeePreview').hide();
        }
    });
});

function generateReport() {
    const form = document.getElementById('acknowledgmentForm');
    const formData = new FormData(form);
    const format = formData.get('format');
    const employeeId = formData.get('employee_id');

    if (!employeeId) {
        alert('يرجى اختيار الموظف');
        return;
    }

    let url = '';
    if (format === 'pdf') {
        url = '{{ route("reports.employee-acknowledgment.pdf") }}';
    } else if (format === 'excel') {
        url = '{{ route("reports.employee-acknowledgment.excel") }}';
    } else if (format === 'preview') {
        previewReport();
        return;
    }

    // Create a form and submit it
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = url;
    tempForm.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';

    const employeeInput = document.createElement('input');
    employeeInput.type = 'hidden';
    employeeInput.name = 'employee_id';
    employeeInput.value = employeeId;

    tempForm.appendChild(csrfInput);
    tempForm.appendChild(employeeInput);
    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
}

function previewReport() {
    const employeeId = document.getElementById('employee_id').value;
    if (!employeeId) {
        alert('يرجى اختيار الموظف');
        return;
    }

    const url = '{{ route("reports.employee-acknowledgment.preview") }}?employee_id=' + employeeId;
    window.open(url, '_blank');
}

function selectMultiple() {
    $('#multipleSelection').toggle();
}

function generateBulkReport() {
    const form = document.getElementById('bulkForm');
    const formData = new FormData(form);
    const employeeIds = formData.getAll('employee_ids[]');

    if (employeeIds.length === 0) {
        alert('يرجى اختيار موظف واحد على الأقل');
        return;
    }

    // Create a form and submit it
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = '{{ route("reports.employee-acknowledgment.bulk") }}';
    tempForm.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';

    tempForm.appendChild(csrfInput);

    employeeIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'employee_ids[]';
        input.value = id;
        tempForm.appendChild(input);
    });

    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
}
</script>
@endsection
