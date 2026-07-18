<!-- Nav tabs -->
<ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
    <li class="nav-item">
        <a class="nav-link fs-14 " data-bs-toggle="tab" href="#overview-tab" role="tab">
            <i class="bx bx-receipt"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.overview') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#complaint" role="tab" onclick="">
            <i class="mdi mdi-file-document-edit"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.complaint') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14 " data-bs-toggle="tab" href="#medical_history" role="tab">
            <i class="bx bx-spreadsheet"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.medical_history') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#general_examination" role="tab">
            <i class="bx bx-spreadsheet"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.general_examination') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#glasses" role="tab">
            <i class="mdi mdi-eye-refresh"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.glasses') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#general_examinationeye" role="tab">
            <i class="bx bx-spreadsheet"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.general_examinationeye') }}</span>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#doctor_order" role="tab" onclick="">
            <i class="bx bxs-bookmark-alt-plus"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.doctor_order') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#diagnosis-history" role="tab" onclick="">
            <i class="bx bxs-bookmark-alt-plus"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.examination_d') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#follow_up" role="tab">
            <i class="bx bx-task"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.follow_up') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#prescription2" role="tab" onclick="">
            <i class="bx bxs-capsule"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.prescription') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#surgery_request" role="tab" onclick="">
            <i class="bx bx-list-plus"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.request_surgery') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14 " data-bs-toggle="tab" href="#medical_sheet" role="tab" onclick="">
            <i class="bx bx-spreadsheet"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.medical_sheet') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#medical_reports" onclick="get_get_report()"
            role="tab">
            <i class="bx bxs-message-alt-edit"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.medical_reports') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#notes_tab" role="tab" onclick="get_notes()">
            <i class="bx bxs-message-rounded-detail"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.notes') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#vital_signs" role="tab">
            <i class="bx bx-pulse"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.vital_signs') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-14" data-bs-toggle="tab" href="#instruction_tab" role="tab">
            <i class="bx bx-pulse"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.instruction') }}</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link fs-14" id="attachments-tab" data-bs-toggle="tab" href="#attachments_tab" role="tab"
            aria-controls="attachments_tab" aria-selected="false">
            <i class="bx bx-paperclip"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.attachments') }}</span>
        </a>
    </li>
</ul>
<div class="flex-shrink-0">

    <form action="{{ route('ophthalmology_examination.end_session', ['id' => $reservations->id]) }}" method="POST"
        onsubmit="return confirm('{{ trans('doctor_trans.confirm_end_ophthalmology_examination_session') }}')">
        @csrf
        <button type="submit" class="btn btn-danger">
            <i class="ri-stop-circle-line align-bottom"></i>
            {{ trans('doctor_trans.end_ophthalmology_examination_session') }}
        </button>
    </form>
    @can('patients-edit')
        <a href="{{ route('patient.edit', ['id' => Crypt::encryptString($patient->id)]) }}" class="btn btn-success">
            <i class="ri-edit-box-line align-bottom"></i> {{ trans('doctor_trans.update_patient_data') }}
        </a>
    @endcan
</div>
