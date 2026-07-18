@extends('layouts.master')

@section('title')
    {{ trans('hr.add') }}
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{ trans('hr.add_employee') }}</h4>
            <div class="page-title-right">
                <button type="button" class="btn btn-warning">
                    <a class="text-white" href="{{ url('/' . ($page = 'employees')) }}">{{ trans('hr.back') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-info">
                <h5 class="mb-0" class="text-white" style="color: #fff;">{{ trans('hr.employee_details') }}</h5>
            </div>
            <div class="card-body">
            @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <form id="myForm" action="{{ route('employees.store') }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="date_of_birth" id="date_of_birth_hidden">
                    <ul class="nav nav-tabs" id="employeeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="meta-tab" data-bs-toggle="tab" data-bs-target="#meta" type="button" role="tab" aria-controls="meta" aria-selected="true">
                                <i class="bi bi-person-badge me-2"></i>{{ trans('hr.meta') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                <i class="bi bi-telephone me-2"></i>{{ trans('hr.contact_info') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hiring-tab" data-bs-toggle="tab" data-bs-target="#hiring" type="button" role="tab" aria-controls="hiring" aria-selected="false">
                                <i class="bi bi-briefcase me-2"></i>{{ trans('hr.hiring_info') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab" aria-controls="certificates" aria-selected="false">
                                <i class="bi bi-award me-2"></i>{{ trans('hr.certificates') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
                                <i class="bi bi-file-earmark-text me-2"></i>{{ trans('hr.documents_details') }}
                            </button>
                        </li>
                
                    </ul>
                    <div class="tab-content" id="employeeTabsContent">
                        <div class="tab-pane fade show active" id="meta" role="tabpanel" aria-labelledby="meta-tab">
                            @include('hr.employees.programs.employees.master_data.meta')
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            @include('hr.employees.programs.employees.master_data.contact')
                        </div>
                        <div class="tab-pane fade" id="hiring" role="tabpanel" aria-labelledby="hiring-tab">
                            @include('hr.employees.programs.employees.master_data.employment')
                        </div>
                        <div class="tab-pane fade" id="certificates" role="tabpanel" aria-labelledby="certificates-tab">
                            <div class="mt-3">
                                <button type="button" class="btn btn-success mb-2" onclick="addCertificateRow()">{{ trans('hr.add') }} {{ trans('hr.certificate_name') }}</button>
                                <table class="table table-bordered" id="certificatesTable">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('hr.certificate_name') }}</th>
                                            <th>{{ trans('hr.certificate_type') }}</th>
                                            <th>{{ trans('hr.graduation_date') }}</th>
                                            <th>{{ trans('hr.university') }}</th>
                                            <th>{{ trans('hr.certificate_file') }}</th>
                                            <th>{{ trans('hr.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <input type="hidden" name="certificates_json" id="certificates_json">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                            @include('hr.employees.programs.employees.master_data.documents')
                        </div>
                       
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <h6>{{ trans('hr.complete_profile') }}</h6>
                        <div class="progress animated-progress custom-progress">
                            <div id="wi" class="progress-bar bg-success" role="progressbar" style="width: 0%"
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <span id="pr" class="progress-label">0%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">{{ trans('hr.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Calculate form completion percentage dynamically
    function calculateCompletionPercentage(formId) {
        const form = document.getElementById(formId);
        const fields = form.querySelectorAll('input, select, textarea');
        const filledFields = Array.from(fields).filter(field => field.value.trim() !== '');
        return (filledFields.length / fields.length) * 100;
    }

    // Update progress bar on field change
    function updateProgressBar() {
        const completionPercentage = calculateCompletionPercentage('myForm');
        const roundedPercentage = Math.round(completionPercentage);
        const progressBar = document.getElementById('wi');
        const progressLabel = document.getElementById('pr');

        progressBar.style.width = `${roundedPercentage}%`;
        progressBar.setAttribute('aria-valuenow', roundedPercentage);
        progressLabel.textContent = `${roundedPercentage}%`;
    }

    // Attach event listeners to form fields
    document.querySelectorAll('#myForm input, #myForm select, #myForm textarea').forEach(field => {
        field.addEventListener('change', updateProgressBar);
    });

    // Ensure the hidden field is updated before submit
    const form = document.getElementById('myForm');
    if(form) {
        form.addEventListener('submit', function() {
            const dob = document.getElementById('date_of_birth');
            const dobHidden = document.getElementById('date_of_birth_hidden');
            if (dob && dobHidden) {
                dobHidden.value = dob.value;
            }
        });
    }

    @php
        $certificateTypes = \App\models\hr\hr_certificates_types::all();
        $universities = \App\models\hr\hr_universities::all();
    @endphp
    const certificateTypes = @json($certificateTypes);
    const universities = @json($universities);
    
    function addCertificateRow(data = {}, index = null) {
        const table = document.getElementById('certificatesTable').getElementsByTagName('tbody')[0];
        const rowCount = table.rows.length;
        const rowIndex = index !== null ? index : rowCount;
        const fileInputName = `certificate_file_${rowIndex}`;
        const fileCell = data.certificate_file ? `<a href="${data.certificate_file}" target="_blank">{{ trans('hr.show') }}</a><br>` : '';
        
        // Create certificate type options
        const certificateTypeOptions = certificateTypes.map(type => 
            `<option value="${type.id}" ${data.certificate_type_id == type.id ? 'selected' : ''}>${type.name_ar}</option>`
        ).join('');
        
        // Create university options
        const universityOptions = universities.map(uni => 
            `<option value="${uni.id}" ${data.university_id == uni.id ? 'selected' : ''}>${uni.name_ar}</option>`
        ).join('');
        
        const row = table.insertRow();
        row.innerHTML = `
            <td><input type="text" class="form-control" name="certificate_name" value="${data.certificate_name || ''}"></td>
            <td><select class="form-control" name="certificate_type_id"><option value="">{{ trans('hr.select_option') }}</option>${certificateTypeOptions}</select></td>
            <td><input type="date" class="form-control" name="graduation_date" value="${data.graduation_date || ''}"></td>
            <td><select class="form-control" name="university_id"><option value="">{{ trans('hr.select_option') }}</option>${universityOptions}</select></td>
            <td>${fileCell}<input type="file" class="form-control" name="${fileInputName}"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeCertificateRow(this)">{{ trans('hr.delete') }}</button></td>
        `;
    }
    function removeCertificateRow(btn) {
        btn.closest('tr').remove();
    }
    // Serialize certificates before submit
    if(form) {
        form.addEventListener('submit', function(e) {
            // ... existing code ...
            const certRows = document.querySelectorAll('#certificatesTable tbody tr');
            const certificates = [];
            certRows.forEach(row => {
                const cells = row.querySelectorAll('input, select');
                const cert = {};
                cells.forEach(cell => {
                    if(cell.type === 'file') {
                        // File will be handled by form-data, skip here
                        cert[cell.name] = '';
                    } else {
                        cert[cell.name] = cell.value;
                    }
                });
                certificates.push(cert);
            });
            document.getElementById('certificates_json').value = JSON.stringify(certificates);
        });
    }
    // On page load, if you have initial certificates, render them with correct file input names
    if (typeof initialCertificates !== 'undefined' && Array.isArray(initialCertificates)) {
        initialCertificates.forEach((cert, idx) => addCertificateRow(cert, idx));
    }
</script>
@endsection
