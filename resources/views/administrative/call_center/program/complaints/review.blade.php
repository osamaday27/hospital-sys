@extends('layouts.masterbooking')

@section('title')
    {{ trans('call_center_trans.review_complaint') }}
@stop

@section('content')
    <!-- رأس الصفحة -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('call_center_trans.review_the_complaint') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('complaints.index') }}">{{ trans('call_center_trans.complaints_mangment') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.review_complaint') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- التنبيهات -->
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <!-- القسم الأول: بيانات المريض وبيانات التواصل -->
    <div class="row">
        <!-- بيانات المريض -->
        <div class="col-md-6">
            <div class="card mb-3">

                <div class="card-header">
                    {{ trans('call_center_trans.patient_data') }}
                </div>
                <div class="card-body">
                    @if(isset($patientInfo))
                        {{-- المريض مسجل --}}
                        <p><strong>{{ trans('call_center_trans.patient_name') }}:</strong> {{ $patientInfo['name'] }}</p>
                        <p><strong>{{ trans('call_center_trans.age') }}:</strong> {{ $patientInfo['age'] }}</p>
                        <p><strong>{{ trans('call_center_trans.patient_gender') }}:</strong> {{ trans('general.' . $patientInfo['gender']) }}</p>
                    @else
                        {{-- المريض غير مسجل --}}
                        <p><strong>{{ trans('call_center_trans.patient_name') }}:</strong> {{ $complaint->patient_name }}</p>
                        <p><strong>{{ trans('call_center_trans.age') }}:</strong> {{ $complaint->patient_age }}</p>
                        <p><strong>{{ trans('call_center_trans.patient_gender') }}:</strong> {{ trans('general.' . $complaint->patient_gender) }}</p>
                    @endif
                </div>

                <div class="card-header">
                    {{ trans('call_center_trans.contact_details') }}
                </div>
                <div class="card-body">
                    @if(is_array($complaint->contact_information) && count($complaint->contact_information) > 0)
                        @foreach($complaint->contact_information as $contact)
                            <p>
                                <strong>{{ trans('call_center_trans.phone') }}:</strong> {{ $contact['phone'] ?? '-' }} |
                                <strong>{{ trans('call_center_trans.email') }}:</strong> {{ $contact['email'] ?? '-' }}
                            </p>
                        @endforeach
                    @else
                        <p>{{ trans('call_center_trans.no_contact_data') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- تفاصيل الشكوى -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    {{ trans('call_center_trans.complaint_details') }}
                </div>
                <div class="card-body">
                    <p><strong>{{ trans('call_center_trans.complaint_date') }}:</strong> {{ $complaint->complaint_date }}</p>
                    <p><strong>{{ trans('call_center_trans.complaint_address') }}:</strong> {{ $complaint->complaint_place }}</p>
                    <p><strong>{{ trans('call_center_trans.complaint_title') }}:</strong> {{ $complaint->title }}</p>
                    <p><strong>{{ trans('call_center_trans.complaint_text') }}:</strong> {{ $complaint->complaint }}</p>
                    @if($complaint->attachments && is_array(json_decode($complaint->attachments, true)))
                        <div class="mt-3">
                            <strong>{{ trans('call_center_trans.attachments') }}:</strong>
                            <ul>
                                @foreach(json_decode($complaint->attachments, true) as $file)
                                    <li><a href="{{ asset('attachments/' . $file) }}" target="_blank">{{ basename($file) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- القسم الثاني: الإجراءات -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">{{ trans('call_center_trans.actions') }}</div>
                <div class="card-body">
                    <!-- عرض الإجراءات السابقة -->
                    <h6>{{ trans('call_center_trans.previous_actions') }}</h6>
                    <ul>
                        @foreach($previousActions as $status)
                                <li>
                                    {{ $status->created_at->format('Y-m-d H:i') }} -
                                    {{ app()->getLocale() == 'ar' ? $status->name_ar : $status->name_en }} -
                                    {{ app()->getLocale() == 'ar' ? 'بواسطة' : 'by' }} -
                                    {{$status->updatedBy->name}} -
                                    {{ $status->action }}
                                </li>
                        @endforeach
                    </ul>

                    <!-- نموذج إضافة إجراء جديد -->
                    <form action="{{ route('complaints.addAction', $complaint->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="new_action" class="form-label">{{ trans('call_center_trans.new_action') }}</label>
                            <textarea name="new_action" id="new_action" class="form-control" rows="3" ></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ trans('call_center_trans.select_status') }}</label>
                            <select name="status" id="statuses" class="form-select" required>
                                <option value="">-- {{ trans('call_center_trans.select') }} --</option>
                                <option value="It has been sent to the department">
                                    {{ app()->getLocale() == 'ar' ? "تم الإرسال إلى القسم" : "It has been sent to the department" }}
                                </option>
                                <option value="The department has responded">
                                    {{ app()->getLocale() == 'ar' ? "تم الرد من القسم" : "The department has responded" }}
                                </option>
                                <option value="The follow-up with the patient has been done">
                                    {{ app()->getLocale() == 'ar' ? "تم المتابعة مع المريض" : "The follow-up with the patient has been done" }}
                                </option>
                                <option value="The issue has been resolved">
                                    {{ app()->getLocale() == 'ar' ? "تم الحل" : "The issue has been resolved" }}
                                </option>
                                <option value="The problem has not been solved">
                                    {{ app()->getLocale() == 'ar' ? "لم يتم الحل" : "The problem has not been solved" }}
                                </option>
                                <option value="closed">
                                    {{ app()->getLocale() == 'ar' ? "مغلق" : "closed" }}
                                </option>
                            </select>
                        </div>
                        <!-- زر العودة -->
                        <a href="{{ route('complaints.index') }}" class="btn btn-secondary ">
                            <i class="fa fa-arrow-left"></i> {{ trans('general.back') }}
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> {{ trans('call_center_trans.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- تضمين JS الخاص بـ Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>

    </script>
@endsection
