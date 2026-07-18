@extends('layouts.master')

@section('title')
    {{ trans('main_trans.modules') }}
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 fw-bold text-primary">
                <i class="mdi mdi-view-grid-outline me-2"></i> {{ __('main_trans.modules') }}
            </h4>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Sidebar: Modules -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-transparent border-bottom">
                <h6 class="mb-0 fw-semibold">{{ __('main_trans.available_modules') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-3" id="modules-list">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3 active" data-module="supply_chain">
                        <div class="module-icon bg-primary bg-opacity-10 p-2 rounded me-3">
                            <i class="mdi mdi-store text-primary fs-5"></i>
                        </div>
                        <div class="module-info">
                            <h6 class="mb-0 fw-semibold">{{ __('supply_chain_trans.title') }}</h6>
                            <small class="text-muted">{{ __('supply_chain_trans.description') }}</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-module="finance">
                        <div class="module-icon bg-success bg-opacity-10 p-2 rounded me-3">
                            <i class="mdi mdi-cash-multiple text-success fs-5"></i>
                        </div>
                        <div class="module-info">
                            <h6 class="mb-0 fw-semibold">{{ __('main_trans.financial_section') }}</h6>
                            <small class="text-muted">{{ __('finance_trans.description') }}</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-module="hr">
                        <div class="module-icon bg-warning bg-opacity-10 p-2 rounded me-3">
                            <i class="mdi mdi-account-group text-warning fs-5"></i>
                        </div>
                        <div class="module-info">
                            <h6 class="mb-0 fw-semibold">{{ __('hr.human_resources') }}</h6>
                            <small class="text-muted">{{ __('hr.description') }}</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-module="medical">
                        <div class="module-icon bg-danger bg-opacity-10 p-2 rounded me-3">
                            <i class="mdi mdi-hospital-building text-danger fs-5"></i>
                        </div>
                        <div class="module-info">
                            <h6 class="mb-0 fw-semibold">{{ __('main_trans.medical_section') }}</h6>
                            <small class="text-muted">{{ __('medical_trans.description') }}</small>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-3" data-module="requests">
                        <div class="module-icon bg-info bg-opacity-10 p-2 rounded me-3">
                            <i class="mdi mdi-file-document-box text-info fs-5"></i>
                        </div>
                        <div class="module-info">
                            <h6 class="mb-0 fw-semibold">{{ __('main_trans.requests_section') }}</h6>
                            <small class="text-muted">{{ __('requests_trans.description') }}</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
        <div id="module-content" class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <!-- Supply Chain Panel -->
            <div class="module-panel card-body p-0" id="supply_chain-panel">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="mdi mdi-store me-2"></i>{{ __('supply_chain_trans.title') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('supply_chain_trans.description') }}</p>
                </div>
                
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-pills nav-justified flex-column flex-md-row mb-4" role="tablist">
                        <li class="nav-item mb-2 mb-md-0">
                            <a class="nav-link active d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#supply_settings">
                                <i class="mdi mdi-cog-outline me-2 d-none d-md-inline"></i>{{ __('main_trans.settings') }}
                            </a>
                        </li>
                        <li class="nav-item mb-2 mb-md-0">
                            <a class="nav-link d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#supply_programs">
                                <i class="mdi mdi-apps me-2 d-none d-md-inline"></i>{{ __('main_trans.programs') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#supply_reports">
                                <i class="mdi mdi-chart-bar me-2 d-none d-md-inline"></i>{{ __('main_trans.reports') }}
                            </a>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content">
                        <!-- Settings -->
                        <div class="tab-pane fade show active" id="supply_settings">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="{{ route('storemovement') }}" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-primary bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-plus-box fs-2 text-primary"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.add_items') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.add_items_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('receiving_inspecting_page') }}" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-success bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-clipboard-check-outline fs-2 text-success"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.receiving_inspecting_items') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.receiving_inspecting_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="#" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-info bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-archive-eye fs-2 text-info"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.inventory_management') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.inventory_management_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Programs -->
                        <div class="tab-pane fade" id="supply_programs">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="{{ route('view_opening_balance') }}" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-warning bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-database-import fs-2 text-warning"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.initial_balances') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.initial_balances_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('transfer_substore') }}" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-info bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-transfer-right fs-2 text-info"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.transfer_to_branch_store') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.transfer_to_branch_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="#" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-purple bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-package-variant-closed fs-2 text-purple"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.supplier_management') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.supplier_management_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reports -->
                        <div class="tab-pane fade" id="supply_reports">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="inventory_audits" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-danger bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-file-document-multiple fs-2 text-danger"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.inventory') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.inventory_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="print_barcode" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-secondary bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-barcode fs-2 text-secondary"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.print_barcode') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.print_barcode_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="#" class="card card-hover text-center p-4 border-0 shadow-sm h-100">
                                        <div class="card-icon bg-teal bg-opacity-10 rounded-circle p-3 mx-auto">
                                            <i class="mdi mdi-chart-timeline-variant fs-2 text-teal"></i>
                                        </div>
                                        <div class="card-body px-0">
                                            <h6 class="card-title fw-semibold mt-3">{{ __('supply_chain_trans.analytics_reports') }}</h6>
                                            <p class="card-text small text-muted">{{ __('supply_chain_trans.analytics_reports_desc') }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance Panel -->
            <div class="module-panel d-none card-body p-0" id="finance-panel">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="fw-bold text-success mb-0"><i class="mdi mdi-cash-multiple me-2"></i>{{ __('main_trans.financial_section') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('finance_trans.description') }}</p>
                </div>
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <i class="mdi mdi-cash-multiple fs-1 text-success opacity-25 mb-3"></i>
                        <h5 class="text-muted">{{ __('finance_trans.coming_soon') }}</h5>
                        <p class="text-muted mb-0">{{ __('finance_trans.module_under_development') }}</p>
                    </div>
                </div>
            </div>

            <!-- HR Panel -->
            <div class="module-panel d-none card-body p-0" id="hr-panel">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="fw-bold text-warning mb-0"><i class="mdi mdi-account-group me-2"></i>{{ __('hr.human_resources') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('hr.description') }}</p>
                </div>
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <i class="mdi mdi-account-group fs-1 text-warning opacity-25 mb-3"></i>
                        <h5 class="text-muted">{{ __('hr.coming_soon') }}</h5>
                        <p class="text-muted mb-0">{{ __('hr.module_under_development') }}</p>
                    </div>
                </div>
            </div>

            <!-- Medical Panel -->
            <div class="module-panel d-none card-body p-0" id="medical-panel">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="fw-bold text-danger mb-0"><i class="mdi mdi-hospital-building me-2"></i>{{ __('main_trans.medical_section') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('medical_trans.description') }}</p>
                </div>
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <i class="mdi mdi-hospital-building fs-1 text-danger opacity-25 mb-3"></i>
                        <h5 class="text-muted">{{ __('medical_trans.coming_soon') }}</h5>
                        <p class="text-muted mb-0">{{ __('medical_trans.module_under_development') }}</p>
                    </div>
                </div>
            </div>

            <!-- Requests Panel -->
            <div class="module-panel d-none card-body p-0" id="requests-panel">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="fw-bold text-info mb-0"><i class="mdi mdi-file-document-box me-2"></i>{{ __('main_trans.requests_section') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('requests_trans.description') }}</p>
                </div>
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <i class="mdi mdi-file-document-box fs-1 text-info opacity-25 mb-3"></i>
                        <h5 class="text-muted">{{ __('requests_trans.coming_soon') }}</h5>
                        <p class="text-muted mb-0">{{ __('requests_trans.module_under_development') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-hover {
    transition: all 0.3s ease;
    border-radius: 12px;
}
.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
}
.module-icon {
    transition: all 0.3s ease;
}
.list-group-item.active .module-icon {
    background-color: rgba(var(--bs-primary-rgb), 0.2) !important;
}
.list-group-item {
    border: none;
    border-radius: 0;
    transition: all 0.2s ease;
}
.list-group-item:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
.list-group-item.active {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-left: 4px solid var(--bs-primary);
    color: var(--bs-dark);
}
.nav-pills .nav-link {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}
.nav-pills .nav-link.active {
    box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.3);
}
.empty-state {
    max-width: 400px;
    margin: 0 auto;
}
.card-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Module switching functionality
    document.querySelectorAll('#modules-list a').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('#modules-list a').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.module-panel').forEach(panel => panel.classList.add('d-none'));
            let panelId = this.getAttribute('data-module') + '-panel';
            document.getElementById(panelId).classList.remove('d-none');
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
