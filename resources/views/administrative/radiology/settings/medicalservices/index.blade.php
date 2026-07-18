@extends('layouts.masterliveware')

@section('title', __('medicalservices.title'))

@section('content')
<div class="container-fluid py-4">

            <h4 class="fw-bold mb-0">
                <i class="mdi mdi-hospital-box-outline me-2"></i> {{ __('medicalservices.list_title') }}
            </h4>
</div>
    {{-- 🚀 محتوى Livewire --}}
    @livewire('radiology.settings.medicalservices.index')

@endsection

{{-- ✅ SweetAlert2 --}}
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'تحذير',
            text: '⚠️ هل أنت متأكد أنك تريد حذف هذا الإجراء؟ لا يمكن التراجع عنه!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: 'نعم، احذفها',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('deleteConfirmed', id);
                Swal.fire('تم الحذف!', '✅ تم الحذف بنجاح.', 'success');
            }
        })
    }
</script>
