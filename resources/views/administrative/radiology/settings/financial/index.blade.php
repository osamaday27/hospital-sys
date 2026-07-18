@extends('layouts.masterliveware')

@section('content')
<div class="container-fluid py-4">

    {{-- 🏷️ العنوان --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-gradient mb-0">
            <i class="mdi mdi-cash-multiple me-2"></i> {{ __('financial.title') }}
        </h3>
    </div>

    {{-- 📌 محتوى الإعدادات المالية --}}
    <div class="row">
        {{-- ربط خدمات الأشعة --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 stylish-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-hospital-box-outline mdi-36px text-primary mb-3"></i>
                    <h5 class="fw-bold">{{ __('financial.link_radiology_services') }}</h5>
                    <p class="text-muted small">{{ __('financial.link_radiology_services_desc') }}</p>
                    <a href="#" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-link-variant me-1"></i> {{ __('financial.manage') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- ربط المعاملات المالية بالحسابات العامة --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 stylish-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-book-open-variant mdi-36px text-warning mb-3"></i>
                    <h5 class="fw-bold">{{ __('financial.link_gl_accounts') }}</h5>
                    <p class="text-muted small">{{ __('financial.link_gl_accounts_desc') }}</p>
                    <a href="#" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-link-variant me-1"></i> {{ __('financial.manage') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- تعريف مراكز التكلفة --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 stylish-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-finance mdi-36px text-success mb-3"></i>
                    <h5 class="fw-bold">{{ __('financial.define_cost_centers') }}</h5>
                    <p class="text-muted small">{{ __('financial.define_cost_centers_desc') }}</p>
                    <a href="#" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-pencil-outline me-1"></i> {{ __('financial.manage') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
