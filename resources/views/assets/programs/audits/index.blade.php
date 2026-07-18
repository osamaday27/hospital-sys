@extends('layouts.master_table')
@section('title', __('assets.audit.index_title'))
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    {{-- ====== Toasts Container ====== --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container" style="z-index:2000"></div>

    {{-- ====== Breadcrumb ====== --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">@lang('assets.audit.index_title')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center"
            style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:999;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show text-center"
            style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:999;">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- =================== أعلى الصفحة: قائمة الجردات =================== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <input type="date" id="f-from" class="form-control" style="width:170px">
                        <input type="date" id="f-to" class="form-control" style="width:170px">
                        <select id="f-status" class="form-select" style="width:170px">
                            <option value="">@lang('assets.audit.all')</option>
                            <option value="draft">@lang('assets.audit.s_draft')</option>
                            <option value="in_progress">@lang('assets.audit.s_in_progress')</option>
                            <option value="reconciled">@lang('assets.audit.s_reconciled')</option>
                            <option value="void">@lang('assets.audit.s_void')</option>
                        </select>
                        <button id="btn-filter" class="btn btn-primary"><i class="ri-search-line"></i></button>
                        <button id="btn-reset-audits" class="btn btn-light">@lang('assets.reset')</button>
                    </div>

                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#startModal">
                        <i class="ri-add-line"></i> @lang('assets.audit.start_new')
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="audits-table" class="table dt-responsive nowrap align-middle w-100 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>@lang('assets.code')</th>
                                    <th>@lang('assets.title')</th>
                                    <th>@lang('assets.status')</th>
                                    <th>@lang('assets.audit.expected')</th>
                                    <th>@lang('assets.audit.scanned')</th>
                                    <th>@lang('assets.audit.matched')</th>
                                    <th>@lang('assets.audit.mismatch')</th>
                                    <th>@lang('assets.audit.missing')</th>
                                    <th>@lang('assets.audit.extra')</th>
                                    <th>@lang('assets.actions')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div id="audits-pager" class="d-flex justify-content-between align-items-center mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== لوحة الجرد المفتوح =================== --}}
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header">
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <div>
                            <strong>@lang('assets.audit.panel_title')</strong>
                            <span id="open-audit-info" class="text-muted ms-2"></span>
                        </div>

                        {{-- سياق المسح --}}
                        <div class="d-flex flex-wrap gap-2 align-items-end">
                            <div>
                                <label class="form-label small mb-1">@lang('assets.location')</label>
                                <select id="ctx-location" class="form-select" style="width:180px">
                                    <option value="">@lang('assets.choose_optional')</option>
                                    @foreach($locations as $L)
                                        <option value="{{ $L['id'] }}">{{ $L['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small mb-1">@lang('assets.store')</label>
                                <select id="ctx-store" class="form-select" style="width:220px">
                                    <option value="">@lang('assets.choose_optional')</option>
                                    @foreach($stores as $S)
                                        <option value="{{ $S['id'] }}">{{ $S['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small mb-1">@lang('assets.status')</label>
                                <select id="ctx-status" class="form-select" style="width:170px">
                                    <option value="">@lang('assets.choose_optional')</option>
                                    @foreach($statusOptions as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label small mb-1">@lang('assets.qty')</label>
                                <input type="number" id="ctx-qty" class="form-control" value="1" min="1" style="width:90px">
                            </div>
                            <button id="ctx-clear" class="btn btn-light" type="button">@lang('assets.clear')</button>
                        </div>

                        {{-- أدوات المسح --}}
                        <div class="d-flex gap-2">
                            <input type="text" id="scan-code" class="form-control" style="width:260px"
                                placeholder="@lang('assets.audit.scan_placeholder')">
                            <button id="btn-scan" class="btn btn-info"><i class="ri-qr-scan-2-line"></i>
                                @lang('assets.audit.scan_btn')</button>
                            <button id="btn-refresh-lines" class="btn btn-secondary"><i
                                    class="ri-refresh-line"></i></button>

                            <button id="btn-export" class="btn btn-outline-primary" disabled>
                                <i class="ri-file-excel-2-line"></i> @lang('assets.audit.export')
                            </button>
                            <button id="btn-reconcile" class="btn btn-success" disabled>
                                <i class="ri-checkbox-circle-line"></i> @lang('assets.audit.reconcile')
                            </button>
                            <button id="btn-void" class="btn btn-outline-danger" disabled>
                                <i class="ri-close-circle-line"></i> @lang('assets.audit.void')
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- فلاتر جدول البنود --}}
                    <div class="row g-2 align-items-end mb-2">
                        <div class="col-12 col-md-2">
                            <label class="form-label small mb-1">@lang('assets.store')</label>
                            <select id="lf-store" class="form-select">
                                <option value="">@lang('assets.all')</option>
                                @foreach($stores as $S)
                                    <option value="{{ $S['id'] }}">{{ $S['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small mb-1">@lang('assets.location')</label>
                            <select id="lf-location" class="form-select">
                                <option value="">@lang('assets.all')</option>
                                @foreach($locations as $L)
                                    <option value="{{ $L['id'] }}">{{ $L['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small mb-1">@lang('assets.status')</label>
                            <select id="lf-status" class="form-select">
                                <option value="">@lang('assets.all')</option>
                                @foreach($statusOptions as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small mb-1">@lang('assets.brand')</label>
                            <select id="lf-brand" class="form-select">
                                <option value="">@lang('assets.all')</option>
                                @foreach($brands as $B)
                                    <option value="{{ $B['id'] }}">{{ $B['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small mb-1">@lang('assets.model')</label>
                            <select id="lf-model" class="form-select">
                                <option value="">@lang('assets.all')</option>
                                @foreach($models as $M)
                                    <option value="{{ $M['id'] }}">{{ $M['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small mb-1">@lang('assets.search')</label>
                            <input type="search" id="lf-q" class="form-control"
                                placeholder="@lang('assets.search_placeholder')">
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button id="lf-apply" class="btn btn-primary">@lang('assets.apply_filters')</button>
                            <button id="lf-reset" class="btn btn-light">@lang('assets.reset')</button>
                        </div>
                    </div>

                    {{-- كروت الأرقام --}}
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <div class="p-2 border rounded text-center">
                                <div class="small text-muted">@lang('assets.audit.expected')</div>
                                <div id="k-expected" class="fw-bold fs-5">0</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 border rounded text-center">
                                <div class="small text-muted">@lang('assets.audit.scanned')</div>
                                <div id="k-scanned" class="fw-bold fs-5">0</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 border rounded text-center">
                                <div class="small text-muted text-success">@lang('assets.audit.matched')</div>
                                <div id="k-matched" class="fw-bold fs-5 text-success">0</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 border rounded text-center">
                                <div class="small text-muted text-warning">@lang('assets.audit.mismatch')</div>
                                <div id="k-mismatch" class="fw-bold fs-5 text-warning">0</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 border rounded text-center">
                                <div class="small text-muted text-danger">@lang('assets.audit.missing')</div>
                                <div id="k-missing" class="fw-bold fs-5 text-danger">0</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 border rounded text-center">
                                <div class="small text-muted text-info">@lang('assets.audit.extra')</div>
                                <div id="k-extra" class="fw-bold fs-5 text-info">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" id="lines-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>@lang('assets.depreciation.asset')</th>
                                    <th>@lang('assets.audit.result')</th>
                                    <th>@lang('assets.location')</th>
                                    <th>@lang('assets.store')</th>
                                    <th>@lang('assets.status')</th>
                                    <th>@lang('assets.qty')</th>
                                    <th>@lang('assets.audit.apply_change')</th>
                                    <th>@lang('assets.audit.scans')</th>
                                    <th>@lang('assets.updated_at')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div id="lines-pager" class="d-flex justify-content-between align-items-center mt-2"></div>

                </div>
            </div>
        </div>
    </div>

    {{-- ====== Start Modal ====== --}}
    <div class="modal fade" id="startModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('assets.programs.audits.start') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">@lang('assets.audit.start_new')</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-2">
                    <div class="col-12"><label class="form-label">@lang('assets.title')</label><input type="text"
                            name="title" class="form-control" maxlength="190"></div>
                    <div class="col-12"><label class="form-label">@lang('assets.notes')</label><input type="text"
                            name="notes" class="form-control" maxlength="1000"></div>
                    <div class="col-6">
                        <label class="form-label">@lang('assets.audit.scope_location')</label>
                        <select name="scope_location_id" class="form-select">
                            <option value="">@lang('assets.choose_optional')</option>
                            @foreach($locations as $L)
                                <option value="{{ $L['id'] }}">{{ $L['name'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">@lang('assets.audit.scope_hint')</div>
                    </div>

                    <div class="col-6">
                        <label class="form-label">@lang('assets.audit.scope_store')</label>
                        <select name="scope_store_id" class="form-select">
                            <option value="">@lang('assets.choose_optional')</option>
                            @foreach($stores as $S)
                                <option value="{{ $S['id'] }}">{{ $S['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer"><button class="btn btn-primary">@lang('assets.audit.start_now')</button></div>
            </form>
        </div>
    </div>

    {{-- ====== Confirm Modal (تأكيدات أنيقة) ====== --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">@lang('assets.confirm')</h6><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="confirmMessage">@lang('assets.are_you_sure')</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('assets.cancel')</button>
                    <button type="button" class="btn btn-primary" id="confirmYes">@lang('assets.ok')</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== Asset Not Found Modal ====== --}}
    <div class="modal fade" id="notFoundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">@lang('assets.audit.asset_not_found_title')</h6><button type="button"
                        class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong>@lang('assets.code'):</strong> <span id="nf-code"></span></div>
                    <div class="text-muted">@lang('assets.audit.asset_not_found_msg')</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('assets.cancel')</button>
                    <button type="button" class="btn btn-primary" id="nf-add">@lang('assets.audit.add_asset')</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== Quick Add Asset Modal (يحفظ كمسودة على سطر الجرد) ====== --}}
    <div class="modal fade" id="newAssetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">@lang('assets.audit.add_asset')</h6><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <input type="hidden" id="na-line-id">
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.code')</label>
                            <input type="text" id="na-code" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.name_ar')</label>
                            <input type="text" id="na-name-ar" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.name_en')</label>
                            <input type="text" id="na-name-en" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.qty')</label>
                            <input type="number" id="na-qty" class="form-control" min="1" value="1">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.brand')</label>
                            <select id="na-brand" class="form-select">
                                <option value="">@lang('assets.choose_optional')</option>
                                @foreach($brands as $B)
                                    <option value="{{ $B['id'] }}">{{ $B['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.model')</label>
                            <select id="na-model" class="form-select">
                                <option value="">@lang('assets.choose_optional')</option>
                                @foreach($models as $M)
                                    <option value="{{ $M['id'] }}">{{ $M['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.location')</label>
                            <select id="na-location" class="form-select">
                                <option value="">@lang('assets.choose_optional')</option>
                                @foreach($locations as $L)
                                    <option value="{{ $L['id'] }}">{{ $L['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.store')</label>
                            <select id="na-store" class="form-select">
                                <option value="">@lang('assets.choose_optional')</option>
                                @foreach($stores as $S)
                                    <option value="{{ $S['id'] }}">{{ $S['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('assets.status')</label>
                            <select id="na-status" class="form-select" required>
                                <option value="in_service">@lang('assets.status_in_service')</option>
                                <option value="maintenance">@lang('assets.status_maintenance')</option>
                                <option value="retired">@lang('assets.status_retired')</option>
                                <option value="scrapped">@lang('assets.status_scrapped')</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-text text-muted mt-2">@lang('assets.audit.add_asset_hint')</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('assets.cancel')</button>
                    <button type="button" class="btn btn-primary" id="na-save">@lang('assets.save')</button>
                </div>
            </div>
        </div>
    </div>

    {{-- JS: Buttons --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        /** ترجمات نتائج الجرد لاستخدامها من JavaScript */
        const RESULT_LABELS = {
            matched: @json(__('assets.audit.result_matched')),
            mismatch: @json(__('assets.audit.result_mismatch')),
            location_mismatch: @json(__('assets.audit.result_location_mismatch')),
            store_mismatch: @json(__('assets.audit.result_store_mismatch')),
            status_mismatch: @json(__('assets.audit.result_status_mismatch')),
            missing: @json(__('assets.audit.result_missing')),
            extra: @json(__('assets.audit.result_extra')),
        };
        /** قوائم الخيارات */
        const LOCATIONS = @json($locations);
        const STORES = @json($stores);
        const STATUSES = @json($statusOptions);

        (function () {
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            // ======== Helpers ========
            function showToast(type, message) {
                const el = document.createElement('div');
                el.className = 'toast align-items-center text-bg-' + (type || 'primary') + ' border-0';
                el.setAttribute('role', 'alert'); el.setAttribute('aria-live', 'assertive'); el.setAttribute('aria-atomic', 'true');
                el.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
                document.getElementById('toast-container').appendChild(el);
                const t = new bootstrap.Toast(el, { delay: 3500 }); t.show();
                el.addEventListener('hidden.bs.toast', () => el.remove());
            }
            function confirmDialog(message) {
                return new Promise(resolve => {
                    document.getElementById('confirmMessage').textContent = message || '@lang("assets.are_you_sure")';
                    const yesBtn = document.getElementById('confirmYes');
                    const m = new bootstrap.Modal(document.getElementById('confirmModal'));
                    const handler = () => { yesBtn.removeEventListener('click', handler); m.hide(); resolve(true); };
                    yesBtn.addEventListener('click', handler);
                    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => resolve(false), { once: true });
                    m.show();
                });
            }
            function buildSelect(optionsArr, selectedId, cls, dataset = {}) {
                const attrs = Object.entries(dataset).map(([k, v]) => `data-${k}="${v}"`).join(' ');
                let html = `<select class="form-select form-select-sm ${cls || ''}" ${attrs}>`;
                html += `<option value="">@lang('assets.choose_optional')</option>`;
                for (const opt of optionsArr) {
                    const sel = String(opt.id) === String(selectedId || '') ? 'selected' : '';
                    html += `<option value="${opt.id}" ${sel}>${opt.name}</option>`;
                }
                html += `</select>`;
                return html;
            }
            function buildStatusSelect(selectedKey, cls, dataset = {}) {
                const attrs = Object.entries(dataset).map(([k, v]) => `data-${k}="${v}"`).join(' ');
                let html = `<select class="form-select form-select-sm ${cls || ''}" ${attrs}>`;
                html += `<option value="">@lang('assets.choose_optional')</option>`;
                for (const [k, label] of Object.entries(STATUSES)) {
                    const sel = String(k) === String(selectedKey || '') ? 'selected' : '';
                    html += `<option value="${k}" ${sel}>${label}</option>`;
                }
                html += `</select>`;
                return html;
            }
            function buildPager(meta, containerId, onPage) {
                const c = document.getElementById(containerId);
                c.innerHTML = '';
                if (!meta || meta.last_page <= 1) return;
                const info = document.createElement('div'); info.className = 'small text-muted';
                info.textContent = `@lang('assets.page') ${meta.current_page} / ${meta.last_page} — @lang('assets.total'): ${meta.total}`;
                const ul = document.createElement('ul'); ul.className = 'pagination mb-0';
                function li(disabled, label, page) {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (disabled ? ' disabled' : '');
                    const a = document.createElement('a'); a.className = 'page-link'; a.href = '#'; a.textContent = label;
                    a.addEventListener('click', (e) => { e.preventDefault(); if (!disabled) onPage(page); });
                    li.appendChild(a); return li;
                }
                ul.appendChild(li(meta.current_page <= 1, '«', 1));
                ul.appendChild(li(meta.current_page <= 1, '‹', meta.current_page - 1));
                ul.appendChild(li(meta.current_page >= meta.last_page, '›', meta.current_page + 1));
                ul.appendChild(li(meta.current_page >= meta.last_page, '»', meta.last_page));
                c.appendChild(info); c.appendChild(ul);
            }

            // ======== State ========
            let openAuditId = null;
            let auditsPage = 1, auditsPerPage = 15;
            let linesPage = 1, linesPerPage = 50;

            // فلاتر البنود
            const lineFilters = { store_id: '', location_id: '', status: '', brand_id: '', model_id: '', q: '' };

            const auditsBody = document.querySelector('#audits-table tbody');
            const linesBody = document.querySelector('#lines-table tbody');

            // ========= تحميل قائمة الجردات =========
            async function loadAudits(page = 1) {
                auditsPage = page;
                const params = new URLSearchParams();
                if (document.querySelector('#f-from').value) params.set('from', document.querySelector('#f-from').value);
                if (document.querySelector('#f-to').value) params.set('to', document.querySelector('#f-to').value);
                if (document.querySelector('#f-status').value) params.set('status', document.querySelector('#f-status').value);
                params.set('page', auditsPage);
                params.set('per_page', auditsPerPage);

                const res = await fetch(`{{ route('assets.programs.audits.ajax') }}?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                const j = await res.json();

                auditsBody.innerHTML = '';
                j.data.forEach(row => {
                    const stClass = row.status === 'reconciled' ? 'success' : (row.status === 'in_progress' ? 'warning' : (row.status === 'void' ? 'secondary' : 'light'));
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
            <td>${row.id}</td>
            <td>${row.code ?? '-'}</td>
            <td>${row.title ?? '-'}</td>
            <td><span class="badge bg-${stClass}">${row.status_label}</span></td>
            <td>${row.expected}</td>
            <td>${row.scanned}</td>
            <td>${row.matched}</td>
            <td>${row.mismatch}</td>
            <td>${row.missing}</td>
            <td>${row.extra}</td>
            <td class="d-flex gap-1 justify-content-center">
                <button class="btn btn-sm btn-outline-primary js-open" data-id="${row.id}">
                  <i class="ri-folder-open-line"></i> @lang('assets.open')
                </button>
                <button class="btn btn-sm btn-outline-secondary js-print" data-id="${row.id}">
                  <i class="ri-printer-line"></i> @lang('assets.print')
                </button>
            </td>`;
                    auditsBody.appendChild(tr);
                });
                document.querySelectorAll('.js-open').forEach(btn => btn.addEventListener('click', () => openAudit(btn.dataset.id)));
                document.querySelectorAll('.js-print').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const url = `{{ route('assets.programs.audits.print', ':id') }}`.replace(':id', btn.dataset.id);
                        window.open(url, '_blank');
                    });
                });

                buildPager(j.meta, 'audits-pager', (p) => loadAudits(p));
            }

            async function openAudit(id) {
                openAuditId = id;
                await loadLines(1);
            }

            // ========= تحميل بنود الجرد =========
            async function loadLines(page = 1) {
                if (!openAuditId) return;
                linesPage = page;

                const params = new URLSearchParams();
                Object.entries(lineFilters).forEach(([k, v]) => { if (v) params.set(k, v); });
                params.set('page', linesPage);
                params.set('per_page', linesPerPage);

                const res = await fetch(`{{ route('assets.programs.audits.lines', ':id') }}`.replace(':id', openAuditId) + `?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                const j = await res.json();

                const canEdit = j.audit.status === 'in_progress';
                document.getElementById('open-audit-info').textContent = `#${j.audit.id} — ${j.audit.status_label}`;

                // تحكم الأزرار والأدوات حسب الحالة
                document.getElementById('btn-export').disabled = false;
                document.getElementById('btn-reconcile').hidden = !canEdit;
                document.getElementById('btn-void').hidden = !canEdit;
                document.getElementById('btn-reconcile').disabled = !canEdit;
                document.getElementById('btn-void').disabled = !canEdit;
                document.getElementById('btn-scan').disabled = !canEdit;
                document.getElementById('scan-code').disabled = !canEdit;
                document.getElementById('ctx-location').disabled = !canEdit;
                document.getElementById('ctx-store').disabled = !canEdit;
                document.getElementById('ctx-status').disabled = !canEdit;
                document.getElementById('ctx-qty').disabled = !canEdit;

                ['expected', 'scanned', 'matched', 'mismatch', 'missing', 'extra'].forEach(k => {
                    document.getElementById('k-' + k).textContent = j.audit[k];
                });

                linesBody.innerHTML = '';
                j.data.forEach((r, idx) => {
                    const badge = r.result === 'matched' ? 'success' : (r.result === 'extra' ? 'info' : (r.result === 'missing' ? 'danger' : 'warning'));
                    const name = r.asset_name ? ` - ${r.asset_name}` : '';

                    const selLoc = buildSelect(LOCATIONS, r.found_loc_id, 'js-edit-loc', { line: r.id });
                    const selStore = buildSelect(STORES, r.found_store_id, 'js-edit-store', { line: r.id });
                    const selStat = buildStatusSelect(r.found_status_key, 'js-edit-status', { line: r.id });
                    const qtyInp = `<input type="number" class="form-control form-control-sm js-edit-qty" data-line="${r.id}" value="${r.found_qty ?? 0}" min="0" style="width:90px">`;

                    const rowNo = (j.meta.current_page - 1) * j.meta.per_page + (idx + 1);

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
            <td>${rowNo}</td>
            <td>[${r.asset_id ?? '-'}] ${r.asset_code ?? ''}${name}</td>
            <td><span class="badge bg-${badge}">${RESULT_LABELS[r.result] || r.result}</span></td>
            <td>${r.exp_loc ?? '-'} ➜ ${selLoc}</td>
            <td>${r.exp_store ?? '-'} ➜ ${selStore}</td>
            <td>${r.exp_status ?? '-'} ➜ ${selStat}</td>
            <td>${(r.exp_qty ?? 1)} ➜ ${qtyInp}</td>
            <td class="text-center"><input type="checkbox" class="form-check-input js-apply" data-id="${r.id}" ${r.apply_change ? 'checked' : ''}></td>
            <td>${r.scan_count}</td>
            <td>${r.last_scanned_at ?? ''}</td>
          `;
                    if (!canEdit) {
                        tr.querySelectorAll('select,input[type="number"],input[type="checkbox"]').forEach(el => el.disabled = true);
                    }
                    linesBody.appendChild(tr);
                });

                // ربط التحرير داخل الصف
                linesBody.querySelectorAll('.js-edit-loc').forEach(el => {
                    el.addEventListener('change', async () => { await updateLine(el.dataset.line, { found_location_id: el.value || null }); showToast('success', '@lang("assets.saved")'); });
                });
                linesBody.querySelectorAll('.js-edit-store').forEach(el => {
                    el.addEventListener('change', async () => { await updateLine(el.dataset.line, { found_store_id: el.value || null }); showToast('success', '@lang("assets.saved")'); });
                });
                linesBody.querySelectorAll('.js-edit-status').forEach(el => {
                    el.addEventListener('change', async () => { await updateLine(el.dataset.line, { found_status: el.value || null }); showToast('success', '@lang("assets.saved")'); });
                });
                linesBody.querySelectorAll('.js-edit-qty').forEach(el => {
                    el.addEventListener('change', async () => {
                        const v = parseInt(el.value || '0', 10);
                        await updateLine(el.dataset.line, { found_qty: (isNaN(v) ? 0 : v) });
                        showToast('success', '@lang("assets.saved")');
                    });
                });
                document.querySelectorAll('.js-apply').forEach(ch => {
                    ch.addEventListener('change', async (e) => {
                        await fetch(`{{ route('assets.programs.audits.apply', ':id') }}`.replace(':id', openAuditId), {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                            body: JSON.stringify({ line_ids: [e.target.dataset.id], apply: e.target.checked })
                        });
                        showToast('success', '@lang("assets.saved")');
                    });
                });

                buildPager(j.meta, 'lines-pager', (p) => loadLines(p));
            }

            async function updateLine(lineId, payload) {
                if (!openAuditId) return;
                try {
                    await fetch(`{{ route('assets.programs.audits.line.update', ['auditId' => ':aid', 'lineId' => ':lid']) }}`
                        .replace(':aid', openAuditId).replace(':lid', lineId), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    await loadLines(linesPage);
                } catch (e) { showToast('danger', '@lang("assets.error_happened")'); }
            }

            // ====== Unknown Asset flow ======
            const nfModal = new bootstrap.Modal(document.getElementById('notFoundModal'));
            const naModal = new bootstrap.Modal(document.getElementById('newAssetModal'));

            function openNotFoundModal(code, lineId, ctx) {
                document.getElementById('nf-code').textContent = code;
                document.getElementById('nf-add').dataset.code = code;
                document.getElementById('nf-add').dataset.lineId = lineId;
                // خزن سياق المسح لاستخدامه كقيم افتراضية
                document.getElementById('nf-add').dataset.ctxLocation = ctx.ctx_location_id || '';
                document.getElementById('nf-add').dataset.ctxStore = ctx.ctx_store_id || '';
                document.getElementById('nf-add').dataset.ctxStatus = ctx.ctx_status || 'in_service';
                document.getElementById('nf-add').dataset.ctxQty = ctx.qty || 1;
                nfModal.show();
            }

            document.getElementById('nf-add').addEventListener('click', () => {
                const btn = document.getElementById('nf-add');
                const code = btn.dataset.code;
                const lineId = btn.dataset.lineId;

                // عبّئ نموذج الإضافة السريعة بالقيم الافتراضية
                document.getElementById('na-line-id').value = lineId;
                document.getElementById('na-code').value = code;
                document.getElementById('na-name-ar').value = '';
                document.getElementById('na-name-en').value = '';
                document.getElementById('na-qty').value = btn.dataset.ctxQty || 1;
                document.getElementById('na-location').value = btn.dataset.ctxLocation || '';
                document.getElementById('na-store').value = btn.dataset.ctxStore || '';
                document.getElementById('na-status').value = btn.dataset.ctxStatus || 'in_service';
                document.getElementById('na-brand').value = '';
                document.getElementById('na-model').value = '';

                nfModal.hide();
                naModal.show();
            });

            document.getElementById('na-save').addEventListener('click', async () => {
                const lineId = document.getElementById('na-line-id').value;
                if (!openAuditId || !lineId) return;

                const payload = {
                    code: document.getElementById('na-code').value.trim(),
                    name_ar: document.getElementById('na-name-ar').value.trim(),
                    name_en: document.getElementById('na-name-en').value.trim(),
                    qty: parseInt(document.getElementById('na-qty').value || '1', 10),
                    brand_id: document.getElementById('na-brand').value || null,
                    model_id: document.getElementById('na-model').value || null,
                    location_id: document.getElementById('na-location').value || null,
                    store_id: document.getElementById('na-store').value || null,
                    status: document.getElementById('na-status').value || 'in_service',
                };

                if (!payload.code || !payload.name_ar) {
                    showToast('warning', '@lang("assets.validation.required_basic")');
                    return;
                }

                try {
                    const res = await fetch(`{{ route('assets.programs.audits.line.draft', ['auditId' => ':aid', 'lineId' => ':lid']) }}`
                        .replace(':aid', openAuditId).replace(':lid', lineId), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) {
                        const j = await res.json().catch(() => ({ message: '' }));
                        showToast('danger', j.message || '@lang("assets.audit.save_draft_error")');
                        return;
                    }
                    showToast('success', '@lang("assets.audit.save_draft_ok")');
                    naModal.hide();
                    await loadLines(1);
                } catch (e) {
                    showToast('danger', '@lang("assets.audit.save_draft_error")');
                }
            });

            // ====== مسح كود ======
            document.getElementById('btn-scan').addEventListener('click', async () => {
                if (!openAuditId) { showToast('warning', '@lang("assets.audit.open_first")'); return; }
                const code = document.getElementById('scan-code').value.trim();
                if (!code) return;

                const ctx = {
                    ctx_location_id: document.getElementById('ctx-location').value || null,
                    ctx_store_id: document.getElementById('ctx-store').value || null,
                    ctx_status: document.getElementById('ctx-status').value || null,
                    qty: parseInt(document.getElementById('ctx-qty').value || '1', 10) || 1
                };
                try {
                    const res = await fetch(`{{ route('assets.programs.audits.scan') }}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: JSON.stringify({ audit_id: openAuditId, code, ...ctx })
                    });
                    const j = await res.json().catch(() => null);
                    if (!res.ok || !j) {
                        showToast('danger', (j && j.message) || '@lang("assets.error_happened")');
                        return;
                    }
                    // لو أصل غير موجود → افتح مودال الاختيار
                    if (j.extra) {
                        openNotFoundModal(code, j.line_id, ctx);
                    } else {
                        showToast('success', '@lang("assets.audit.scan_done")');
                    }
                    document.getElementById('scan-code').value = '';
                    await loadLines(1);
                } catch (e) { showToast('danger', '@lang("assets.error_happened")'); }
            });
            document.getElementById('scan-code').addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); document.getElementById('btn-scan').click(); }
            });
            document.getElementById('ctx-clear').addEventListener('click', () => {
                ['ctx-location', 'ctx-store', 'ctx-status'].forEach(id => document.getElementById(id).value = '');
                document.getElementById('ctx-qty').value = 1;
            });

            // فلاتر البنود
            document.getElementById('lf-apply').addEventListener('click', () => {
                lineFilters.store_id = document.getElementById('lf-store').value;
                lineFilters.location_id = document.getElementById('lf-location').value;
                lineFilters.status = document.getElementById('lf-status').value;
                lineFilters.brand_id = document.getElementById('lf-brand').value;
                lineFilters.model_id = document.getElementById('lf-model').value;
                lineFilters.q = document.getElementById('lf-q').value.trim();
                loadLines(1);
            });
            document.getElementById('lf-reset').addEventListener('click', () => {
                ['lf-store', 'lf-location', 'lf-status', 'lf-brand', 'lf-model'].forEach(id => document.getElementById(id).value = '');
                document.getElementById('lf-q').value = '';
                Object.keys(lineFilters).forEach(k => lineFilters[k] = '');
                loadLines(1);
            });

            // أزرار أعلى لوحة البنود
            document.getElementById('btn-refresh-lines').addEventListener('click', () => loadLines(linesPage));
            document.getElementById('btn-filter').addEventListener('click', () => loadAudits(1));
            document.getElementById('btn-reset-audits').addEventListener('click', () => {
                ['f-from', 'f-to', 'f-status'].forEach(id => document.getElementById(id).value = '');
                loadAudits(1);
            });

            document.getElementById('btn-reconcile').addEventListener('click', async () => {
                if (!openAuditId) return;
                if (!await confirmDialog('@lang("assets.audit.reconcile_confirm")')) return;
                const res = await fetch(`{{ route('assets.programs.audits.reconcile', ':id') }}`.replace(':id', openAuditId), {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'text/html' }
                });
                showToast('success', '@lang("assets.audit.reconciled_ok")');
                if (res.redirected) location.href = res.url;
                else { await loadLines(); await loadAudits(auditsPage); }
            });

            document.getElementById('btn-void').addEventListener('click', async () => {
                if (!openAuditId) return;
                if (!await confirmDialog('@lang("assets.audit.void_confirm")')) return;
                const res = await fetch(`{{ route('assets.programs.audits.void', ':id') }}`.replace(':id', openAuditId), {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'text/html' }
                });
                showToast('success', '@lang("assets.audit.void_ok")');
                if (res.redirected) location.href = res.url;
                else { await loadAudits(auditsPage); await loadLines(); }
            });

            document.getElementById('btn-export').addEventListener('click', () => {
                if (!openAuditId) return; window.location = `{{ route('assets.programs.audits.export', ':id') }}`.replace(':id', openAuditId);
            });

            // التحميل الأولي
            loadAudits();
        })();
    </script>
@endsection
