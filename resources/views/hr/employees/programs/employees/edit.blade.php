@extends('layouts.master')
@section('title')
Edit Employee
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{ trans('hr.edit_employee') }}</h4>
            <div class="page-title-right">
                <button type="button" class="btn btn-warning">
                    <a class="text-white" href="{{ url()->previous() }}">{{ trans('hr.back') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-info">
                <h5 class="mb-0" class="text-white">{{ trans('hr.employee_details') }}</h5>
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
                <form id="myForm" action="{{ route('employees.update', $employee->id) }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

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
                            <div class="row mt-2">
                                <div class="col-3">
                                    <!-- Profile Image Section -->
                                    <div class="text-center mb-2 mt-4">
                                        <div class="profile-user position-relative d-inline-block mx-auto">
                                            <img src="{{ $employee->employee_photo ? asset($employee->employee_photo) : asset('assets/images/users/user-dummy-img.jpg') }}"
                                                class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                                
                                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                <input id="profile-img-file-input" type="file" class="profile-img-file-input"
                                                    accept="image/png, image/jpeg" name="employee_photo" onchange="get();">
                                                <label for="profile-img-file-input" class="avatar-xs">
                                                    <span class="avatar-title rounded-circle bg-light text-body">
                                                        <i class="ri-camera-fill"></i>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <!-- Gender Select -->
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">{{ trans('hr.gender') }}</label>
                                        <select id="gender" class="js-example-basic-single" name="gender" onchange="get();">
                                            <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>
                                                {{ trans('hr.Male') }}
                                            </option>
                                            <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>
                                                {{ trans('hr.Female') }}
                                            </option>
                                        </select>
                                    </div>

                                </div>
                            
                                <div class="col-9">
                                    <div class="row">
                                        <!-- Full Name Input -->
                                        <div class="col-12 mb-3 mt-3">
                                            <label for="full_name" class="form-label">{{ trans('hr.full_name') }}</label>
                                            <input type="text" onchange="splitAndTranslateName(this.value),get();" class="form-control" value="{{ $employee->full_name }}" id="full_name" name="full_name">
                                        </div>
                            
                                        <!-- Arabic Name Fields (First, Second, Third, Fourth) -->
                                        @foreach(['first_name_ar', 'second_name_ar', 'third_name_ar', 'fourth_name_ar'] as $name)
                                            <div class="col-3 mb-3">
                                                <label for="{{ $name }}" class="form-label">{{ trans("hr.{$name}") }}</label>
                                                <input type="text" class="form-control" name="{{ $name }}" id="{{ $name }}"
                                                    onchange="get();" value="{{ $employee -> $name }}">
                                            </div>
                                        @endforeach
                            
                                        <!-- English Name Fields (First, Second, Third, Fourth) -->
                                        @foreach(['first_name_en', 'second_name_en', 'third_name_en', 'fourth_name_en'] as $name)
                                            <div class="col-3 mb-3">
                                                <label for="{{ $name }}" class="form-label">{{ trans("hr.{$name}") }}</label>
                                                <input type="text" class="form-control" name="{{ $name }}" id="{{ $name }}"
                                                    onchange="get();" placeholder="{{ trans('hr.enter_'.$name.'_name_en') }}" value="{{ $employee -> $name }}">
                                            </div>
                                        @endforeach

                                        <div class="col-3 mb-3">
                                            <label for="employee_code" class="form-label">{{ trans("hr.employee_code") }}</label>
                                            <input type="text" class="form-control" name="employee_code" id="employee_code"
                                                onchange="get();" placeholder="{{ trans('hr.enter_employee_code') }}" value="{{ $employee -> employee_code }}">
                                        </div>

                                        <div class="col-3 mb-3">
                                            <label for="gahar_code" class="form-label">{{ trans("hr.gahar_code") }}</label>
                                            <input type="text" class="form-control" name="gahar_code" id="gahar_code"
                                                onchange="get();" placeholder="{{ trans('hr.enter_gahar_code') }}" value="{{ $employee -> gahar_code }}">
                                        </div>

                                        <div class="col-3 mb-3">
                                            <label for="employee_status" class="form-label">{{ trans('hr.employee_status') }}</label>
                                            <select id="employee_status" class="js-example-basic-single" name="employee_status" onchange="get();">
                                                <option value="in_service" {{ $employee->employee_status == 'in_service' ? 'selected' : '' }}>
                                                    {{ trans('hr.in_service') }}
                                                </option>
                                                <option value="out_of_service" {{ $employee->employee_status == 'out_of_service' ? 'selected' : '' }}>
                                                    {{ trans('hr.out_of_service') }}
                                                </option>
                                            </select>
                                        </div>
                            
                                        <!-- Marital Status -->
                                        <div class="col-3 mb-3">
                                            <label for="marital_status" class="form-label">{{ trans('hr.marital_status') }}</label>
                                            <select id="marital_status" class="js-example-basic-single" name="marital_status" onchange="get();">
                                                <option value="single" {{ $employee->marital_status == 'single' ? 'selected' : '' }}>
                                                    {{ trans('hr.single') }}
                                                </option>
                                                <option value="married" {{ $employee->marital_status == 'married' ? 'selected' : '' }}>
                                                    {{ trans('hr.married') }}
                                                </option>
                                                <option value="widower" {{ $employee->marital_status == 'widower' ? 'selected' : '' }}>
                                                    {{ trans('hr.widower') }}
                                                </option>
                                                <option value="divorced" {{ $employee->marital_status == 'divorced' ? 'selected' : '' }}>
                                                    {{ trans('hr.divorced') }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Identification and Passport Numbers -->
                                        @foreach(['identification_number'] as $field)
                                            <div class="col-3 mb-3">
                                                <label for="{{ $field }}" class="form-label">{{ trans("hr.{$field}") }}</label>
                                                <input type="text" class="form-control" name="{{ $field }}" id="{{ $field }}" onchange="autoFillFromID(this.value);get();"
                                                    placeholder="{{ trans("hr.{$field}") }}" value="{{ $employee->$field }}">
                                            </div>
                                        @endforeach
                                        <div class="col-3 mb-3">
                                            <label for="date_of_birth" class="form-label">{{ trans('hr.date_of_birth') }}</label>
                                            <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" onchange="get();" value="{{ $employee->date_of_birth }}">
                                        </div>
                                        <script>
                                        function autoFillFromID(id) {
                                            if (id.length === 14) {
                                                let century = id[0];
                                                let year = id.substring(1, 3);
                                                let month = id.substring(3, 5);
                                                let day = id.substring(5, 7);
                                                let genderDigit = id[12];
                                                let fullYear = '';
                                                if (century === '2') fullYear = '19' + year;
                                                else if (century === '3') fullYear = '20' + year;
                                                if (month >= 1 && month <= 12 && day >= 1 && day <= 31) {
                                                    document.getElementById('date_of_birth').value = `${fullYear}-${month}-${day}`;
                                                }
                                                if (genderDigit % 2 === 0) {
                                                    document.getElementById('gender').value = 'Female';
                                                } else {
                                                    document.getElementById('gender').value = 'Male';
                                                }
                                            }
                                        }
                                        </script>
                                        @foreach(['passport_number'] as $field)
                                            <div class="col-3 mb-3">
                                                <label for="{{ $field }}" class="form-label">{{ trans("hr.{$field}") }}</label>
                                                <input type="text" class="form-control" name="{{ $field }}" id="{{ $field }}" onchange="get();"
                                                    placeholder="{{ trans("hr.{$field}") }}" value="{{ $employee->$field }}">
                                            </div>
                                        @endforeach
                            
                                        <!-- Religion -->
                                        <div class="col-3 mb-3">
                                            <label for="religion" class="form-label">{{ trans('hr.religion') }}</label>
                                            <select id="religion" class="js-example-basic-single" name="religion" onchange="get();">
                                                <option value="muslim" {{ $employee->religion == 'muslim' ? 'selected' : '' }}>
                                                    {{ trans('hr.muslim') }}
                                                </option>
                                                <option value="christian" {{ $employee->religion == 'christian' ? 'selected' : '' }}>
                                                    {{ trans('hr.christian') }}
                                                </option>
                                                <option value="other" {{ $employee->religion == 'other' ? 'selected' : '' }}>
                                                    {{ trans('hr.other') }}
                                                </option>
                                            </select>
                                        </div>

                            
                                        <!-- Country, City, Area, Full Address -->
                                        @foreach(['country', 'city', 'district', 'full_address'] as $field)
                                            <div class="col-3 mb-3">
                                                @if($field == 'country')
                                                    <label for="{{ $field }}" class="form-label">{{ trans('hr.country') }}</label>
                                                    <select id="{{ $field }}" class="js-example-basic-single" name="{{ $field }}" onchange="get();">
                                                        <option value="">{{ trans('hr.select_option') }}</option>
                                                        @foreach ($country as $data)
                                                            <option value="{{ $data->id }}" {{ $employee->country == $data->id ? 'selected' : '' }}>{{ $data->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field == 'city')
                                                    <label for="{{ $field }}" class="form-label">{{ trans('hr.city') }}</label>
                                                    <select id="{{ $field }}" class="js-example-basic-single" name="{{ $field }}" onchange="get();" value="{{ $employee->$field }}">
                                                        <option value="" selected>{{ trans('hr.select_option') }}</option>
                                                    </select>
                                                @else
                                                    <label for="{{ $field }}" class="form-label">{{ trans('hr.' . $field) }}</label>
                                                    <textarea class="form-control" name="{{ $field }}" id="{{ $field }}" onchange="get();" 
                                                        placeholder="{{ trans('hr.' . $field) }}" rows="4">{{ $employee->$field }}</textarea>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row mt-2">
                                <!-- Phone -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">{{ trans('hr.phone') }}</label>
                                        <input type="text" class="form-control" name="phone" id="phone" placeholder="{{ trans('hr.phone') }}" value="{{ $employee->phone }}" />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ trans('hr.email') }}</label>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="{{ trans('hr.email') }}" value="{{ $employee->email }}" />
                                    </div>
                                </div>

                                <!-- Landline -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <label for="landline" class="form-label">{{ trans('hr.landline') }}</label>
                                        <input type="text" class="form-control" name="landline" id="landline" placeholder="{{ trans('hr.landline') }}" value="{{ $employee->landline }}" />
                                    </div>
                                </div>

                                <!-- Fax -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <label for="fax" class="form-label">{{ trans('hr.fax') }}</label>
                                        <input type="text" class="form-control" name="fax" id="fax" placeholder="{{ trans('hr.fax') }}" value="{{ $employee->fax }}" />
                                    </div>
                                </div>

                                <!-- Emergency Contact Name -->
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="emergency_name" class="form-label">{{ trans('hr.emergency_name') }}</label>
                                        <input type="text" class="form-control" name="emergency_name" id="emergency_name" placeholder="{{ trans('hr.emergency_name') }}" value="{{ $employee->emergency_name }}" />
                                    </div>
                                </div>

                                <!-- Emergency Contact Number -->
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="emergency_number" class="form-label">{{ trans('hr.emergency_number') }}</label>
                                        <input type="text" class="form-control" name="emergency_number" id="emergency_number" placeholder="{{ trans('hr.emergency_number') }}" value="{{ $employee->emergency_number }}" />
                                    </div>
                                </div>

                                <!-- Relationship -->
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="relationship" class="form-label">{{ trans('hr.relationship') }}</label>
                                        <input type="text" class="form-control" name="relationship" id="relationship" placeholder="{{ trans('hr.relationship') }}" value="{{ $employee->relationship }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="hiring" role="tabpanel" aria-labelledby="hiring-tab">
                            <div class="row mt-2">
                                <div class="col-3 mb-3">
                                    <label for="employee_type" class="form-label">{{ trans('hr.employee_type') }}</label>
                                    <select id="employee_type" class="js-example-basic-single" name="employee_type" onchange="get();">
                                        <option value="physician" {{ $employee->employee_type == 'physician' ? 'selected' : '' }}>
                                            {{ trans('hr.physician') }}
                                        </option>
                                        <option value="nursing" {{ $employee->employee_type == 'nursing' ? 'selected' : '' }}>
                                            {{ trans('hr.nursing') }}
                                        </option>
                                        <option value="admin" {{ $employee->employee_type == 'admin' ? 'selected' : '' }}>
                                            {{ trans('hr.admin') }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Schedule Selection -->
                                <div class="col-3 mb-3">
                                    <label for="schedule_id" class="form-label">{{ trans('hr.schedule') }}</label>
                                    <select id="schedule_id" class="js-example-basic-single" name="schedule_id" onchange="get();">
                                        <option value="">{{ trans('hr.select_option') }}</option>
                                        @if(isset($schedules))
                                            @foreach($schedules as $schedule)
                                                <option value="{{ $schedule->id }}" {{ old('schedule_id', $employee->schedule_id) == $schedule->id ? 'selected' : '' }}>
                                                    {{ $schedule->name }} ({{ number_format($schedule->total_hours ?? 0, 2) }} hrs)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Department -->
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">{{ trans('hr.department') }}</label>
                                    <select name="department" id="department" class="js-example-basic-single">
                                        <option value="">--Select--</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                @if(old('department', $employee->department) == $department->id) selected @endif>
                                                {{ $department->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Section -->
                                <div class="col-md-6 mb-3">
                                    <label for="section" class="form-label">{{ trans('hr.section') }}</label>
                                    <select name="section" id="section" class="js-example-basic-single">
                                        <option value="">--Select--</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" 
                                                @if(old('section', $employee->section) == $section->id) selected @endif>
                                                {{ $section->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Job Position -->
                                <div class="col-md-6 mb-3">
                                    <label for="job_position" class="form-label">{{ trans('hr.job_position') }}</label>
                                    <select name="job_position" id="job_position" class="js-example-basic-single">
                                        <option value="">--Select--</option>
                                        @foreach($jobs as $job)
                                            <option value="{{ $job->id }}" 
                                                @if(old('job_position', $employee->job_position) == $job->id) selected @endif>
                                                {{ $job->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Contract Open Date -->
                                <div class="col-md-6 mt-3">
                                    <label for="contract_open_date" class="form-label">{{ trans('hr.contract_open_date') }}</label>
                                    <input type="date" class="form-control" name="contract_open_date" id="contract_open_date" value="{{ $employee->contract_open_date }}">
                                </div>

                                <!-- Hiring Date -->
                                <div class="col-md-6 mt-3">
                                    <label for="hiring_date" class="form-label">{{ trans('hr.hiring_date') }}</label>
                                    <input type="date" class="form-control" name="hiring_date" id="hiring_date" value="{{ $employee->hiring_date }}">
                                </div>

                                <!-- Contract End Date -->
                                <div class="col-md-6 mt-3">
                                    <label for="contract_end_date" class="form-label">{{ trans('hr.contract_end_date') }}</label>
                                    <input type="date" class="form-control" name="contract_end_date" id="contract_end_date" value="{{ $employee->contract_end_date }}">
                                </div>
                            </div>
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
                                    <tbody>
                                        @if(isset($certificates))
                                            @foreach($certificates as $idx => $cert)
                                                <tr>
                                                    <td><input type="text" class="form-control" name="certificate_name" value="{{ $cert->certificate_name }}"></td>
                                                    <td>
                                                        <select class="form-control" name="certificate_type_id">
                                                            <option value="">{{ trans('hr.select_option') }}</option>
                                                            @php
                                                                $certificateTypes = \App\models\hr\hr_certificates_types::all();
                                                            @endphp
                                                            @foreach($certificateTypes as $type)
                                                                <option value="{{ $type->id }}" {{ $cert->certificate_type_id == $type->id ? 'selected' : '' }}>{{ $type->name_ar }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="date" class="form-control" name="graduation_date" value="{{ $cert->graduation_date }}"></td>
                                                    <td>
                                                        <select class="form-control" name="university_id">
                                                            <option value="">{{ trans('hr.select_option') }}</option>
                                                            @php
                                                                $universities = \App\models\hr\hr_universities::all();
                                                            @endphp
                                                            @foreach($universities as $uni)
                                                                <option value="{{ $uni->id }}" {{ $cert->university_id == $uni->id ? 'selected' : '' }}>{{ $uni->name_ar }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        @if($cert->certificate_file)
                                                            <a href="{{ asset($cert->certificate_file) }}" target="_blank">{{ trans('hr.show') }}</a><br>
                                                        @endif
                                                        <input type="file" class="form-control" name="certificate_file_{{ $idx }}">
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeCertificateRow(this)">{{ trans('hr.delete') }}</button></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <input type="hidden" name="certificates_json" id="certificates_json">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                            <div class="row mt-2">
                                @php
                                    $documents = [
                                        'cv' => 'السيره الذاتيه',
                                        'militery' => 'شهادة الجيش',
                                        'graduation_certificate' => 'شهاده التخرج',
                                        'birth_certificate' => 'شهاده الميلاد',
                                        'id_front' => 'البطاقه الشخصيه (اماميه)',
                                        'id_back' => 'البطاقه الشخصيه (خلفيه)',
                                        'criminality' => 'الفيش الجنائي',
                                        'insurance_print' => 'برنت تائميني',
                                        'kaab' => 'كعب العمل',
                                        'work_permit' => 'ترخيص مزولة المهنة',
                                        'union_card' => 'كارنية النقابة',
                                        'union_desc' => 'الوصف النقابي',
                                        '111_form' => 'نموذج 111',
                                        'contract_document' => 'صورة العقد',
                                        'declarations' => 'الاقرارات',
                                        'gen_spec_prep' => 'التهيئة العامة والخاصة',
                                        'privileges' => 'الامتيازات',
                                        'vaccinations' => 'التطعيمات',
                                        'certificates' => 'الشهادات',
                                        'internship_certificate' => 'شهادة التدريب',
                                        'experience_certificate' => 'شهادة الخبرة',
                                        'personal_photo' => 'الصورة الشخصية',
                                        'other_documents' => 'مستندات أخرى',
                                        'full_document' => 'الملف كامل'
                                    ];
                                @endphp
                            
                                @foreach($documents as $key => $label)
                                    <div class="col-md-6 mt-3">
                                        <label for="{{ $key }}" class="form-label">{{ $label }}</label>
                                        @php
                                            $filePath = $employee->$key;
                                            // لو موجود قيمة
                                            if (!empty($filePath)) {
                                                // حاول تحويلها JSON
                                                $decoded = json_decode($filePath, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    // لو نجحت وحصلت Array، خد أول عنصر
                                                    $filePath = $decoded[0];
                                                }
                                            }
                                        @endphp
                                        @if(!empty($filePath))
                                            <div>
                                                <a href="{{ asset('attachments/' . ltrim($filePath, '/')) }}" target="_blank" class="btn btn-link">
                                                    عرض الملف الحالي
                                                </a>
                                                
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" name="{{ $key }}" id="{{ $key }}" accept=".pdf,.jpg,.png">
                                        <small class="form-text text-muted">{{ trans('hr.upload') }}</small>
                                    </div>
                                @endforeach
                            </div>
                            
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
                        <button type="submit" class="btn btn-primary">{{ trans('hr.edit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>


function normalizeName(name) {
    return name.replace(/[أإآ]/g, 'ا');
}

// دالة تقسيم الاسم وترجمته
async function splitAndTranslateName(fullName) {
    const normalizedFullName = normalizeName(fullName.trim());
    const nameParts = normalizedFullName.split(/\s+/);

    // ملء الحقول العربية
    updateArabicFields(nameParts);

    // ترجمة الأسماء إلى الإنجليزية
    const translatedParts = await translateToEnglish(nameParts);

    // ملء الحقول الإنجليزية
    updateEnglishFields(translatedParts);
}

// دالة لملء الحقول العربية
function updateArabicFields(nameParts) {
    document.getElementById('first_name_ar').value = nameParts[0] || '';
    document.getElementById('second_name_ar').value = nameParts[1] || '';
    document.getElementById('third_name_ar').value = nameParts[2] || '';
    document.getElementById('fourth_name_ar').value = nameParts[3] || '';
    if (nameParts.length > 4) {
        document.getElementById('fourth_name_ar').value += " " + nameParts.slice(4).join(' ');
    }
}

// دالة لملء الحقول الإنجليزية
function updateEnglishFields(nameParts) {
    document.getElementById('first_name_en').value = nameParts[0] || '';
    document.getElementById('second_name_en').value = nameParts[1] || '';
    document.getElementById('third_name_en').value = nameParts[2] || '';
    document.getElementById('fourth_name_en').value = nameParts[3] || '';
}

async function translateToEnglish(nameParts) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // الحصول على الـ CSRF Token
    
    try {
        // إرسال طلب إلى السيرفر للحصول على الترجمات
        const response = await fetch('/translate-names', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token, // إضافة الـ CSRF Token إلى رأس الطلب
            },
            body: JSON.stringify({ names: nameParts }),
        });

        if (!response.ok) {
            throw new Error('Failed to fetch translations');
        }

        const translations = await response.json();

        return nameParts.map(name => translations[name] || name);
    } catch (error) {
        console.error('Error during translation:', error);
        return nameParts;
    }
}



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
    const form = document.getElementById('myForm');
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
