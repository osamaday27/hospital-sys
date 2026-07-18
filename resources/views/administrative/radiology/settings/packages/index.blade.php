@extends('layouts.masterliveware')

@section('title', __('packages.title'))

@section('content')
<div class="container-fluid py-4">

    {{-- 🏷️ العنوان --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-gradient mb-0">
            <i class="mdi mdi-package-variant me-2"></i> {{ __('radiology_trans.packages_title') }}
        </h4>
    </div>

    {{-- 🚀 استدعاء كومبوننت Livewire --}}
        @livewire('radiology.settings.packages.index')

</div>
@endsection
