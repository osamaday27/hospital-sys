@extends('layouts.masterbooking')

@section('title', trans('call_center_trans.upload_attachments'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('call_center_trans.upload_attachments') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('all_questionnaires.index') }}">{{ trans('call_center_trans.questionnaires_mangment') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.upload_attachments') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- رفع المرفقات -->
    <div class="card">
        <div class="card-header">
            <strong>{{ trans('call_center_trans.upload_attachments') }}</strong>
        </div>
        <div class="card-body">
            <form id="uploadForm" action="{{ route('uploadAttachments.store', ['id' => Crypt::encryptString($questionnaire->id)]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="attachments" class="form-label">{{ trans('call_center_trans.select_files') }}</label>
                    <input type="file" name="attachments[]" id="attachments" class="form-control" multiple required>
                    <div class="text-danger mt-1">
                        {{ trans('call_center_trans.max_size') }}: 5MB
                    </div>
                </div>
                <button type="submit" id="saveAttachments" class="btn btn-primary">
                    <i class="fas fa-upload"></i> {{ trans('general.save') }}
                </button>
            </form>
        </div>
    </div>

    <!-- قائمة الملفات المرفوعة -->
    @if ($questionnaire->attachments)
        <div class="card mt-3">
            <div class="card-header">
                <h5>{{ trans('call_center_trans.previous_attachments') }}</h5>
            </div>
            <div class="card-body">
                @php
                    $attachments = json_decode($questionnaire->attachments, true) ?? [];
                @endphp
                @if(count($attachments) > 0)
                    <ul id="fileList" class="list-group">
                        @foreach ($attachments as $index => $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ asset('attachments/' .$file) }}" target="_blank">{{ basename($file) }}</a>

                                <button class="btn btn-danger btn-sm delete-attachment" data-file="{{ $file }}" data-index="{{ $index }}">
                                    <i class="fas fa-trash">{{ trans('general.delete') }}</i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">{{ trans('call_center_trans.no_attachments') }}</p>
                @endif
            </div>
        </div>
    @endif

    <!-- زر الرجوع -->
    <div class="fixed-bottom bg-light p-3 text-end shadow border-top">
        <a href="{{ route('all_questionnaires.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> {{ trans('general.back') }}
        </a>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
 $(document).ready(function () {
    // عند رفع الملفات
    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    console.log(response); // تحقق من القيم الراجعة

                    Swal.fire({
                        icon: "success",
                        text: "{{ trans('call_center_trans.files_uploaded_successfully') }}",
                        showConfirmButton: true
                    }).then(() => {
                        location.reload(); // إعادة تحميل الصفحة بعد الحفظ
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    text: "{{ trans('call_center_trans.error_uploading_files') }}",
                    showConfirmButton: true
                });
            }
        });
    });

    // دالة حذف المرفقات
    function attachDeleteEvent() {
    $('.delete-attachment').off('click').on('click', function () {
        var filePath = $(this).data('file');
        var questionnaireId = "{{ Crypt::encryptString($questionnaire->id) }}";
        var $button = $(this);

        Swal.fire({
            title: "{{ trans('call_center_trans.confirm_delete') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "{{ trans('call_center_trans.yes_delete') }}",
            cancelButtonText: "{{ trans('general.cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('deleteAttachment') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        file: filePath,
                        questionnaire_id: questionnaireId
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                text: "{{ trans('call_center_trans.file_deleted_successfully') }}",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // حذف العنصر من القائمة بدون تحديث الصفحة
                            $button.closest('li').remove();
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: "error",
                            text: xhr.responseJSON?.error || "{{ trans('call_center_trans.error_deleting_file') }}",
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });
}



    // تشغيل دالة الحذف عند تحميل الصفحة
    attachDeleteEvent();
});


</script>

