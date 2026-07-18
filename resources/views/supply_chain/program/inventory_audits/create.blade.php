@extends('layouts.master_table')

@section('title')
{{ __('supply_chain_trans.create_inventory_audit') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Toasts --}}
    <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

    <div class="row g-3">
        {{-- القسم العلوي --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><strong>{{ __('supply_chain_trans.inventory_audits') }}</strong></div>
                <div class="card-body">
                    <form id="audit-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="store_id" value="{{ $store->id }}">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('supply_chain_trans.code') }}</label>
                                <input type="text" class="form-control" name="code" value="{{ $code }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                                <input type="text" class="form-control"
                                    value="{{ $store->name_ar ?? $store->name_en ?? $store->name ?? ('#' . $store->id) }}"
                                    readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('supply_chain_trans.audit_date') }}</label>
                                <input type="date" class="form-control" name="audit_date"
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('supply_chain_trans.start_date') }}</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mt-2">{{ __('supply_chain_trans.end_date') }}</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                            <div class="col-md-9">
                                <label class="form-label mt-2">{{ __('supply_chain_trans.notes') }}</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- القسم الثاني: أدوات الإضافة + الجدول --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                    {{-- مودال أرصدة المخزن --}}
                    <button id="btn-open-balances" class="btn btn-outline-primary">
                        <i class="ri-database-2-line"></i> {{ __('supply_chain_trans.store_balances') }}
                    </button>

                    {{-- إضافة يدوي: منتج/مجموعة --}}
                    <select id="product-select" class="form-control" style="width:300px"></select>
                    <button id="btn-add-product"
                        class="btn btn-secondary">{{ __('supply_chain_trans.add_product') }}</button>

                    <select id="category-select" class="form-control" style="width:260px"></select>
                    <button id="btn-add-category"
                        class="btn btn-secondary">{{ __('supply_chain_trans.add_category') }}</button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="items-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px">{{ __('supply_chain_trans.serial') }}</th>
                                    <th>{{ __('supply_chain_trans.product') }}</th>
                                    <th>{{ __('supply_chain_trans.product_code') }}</th>
                                    <th>{{ __('supply_chain_trans.batch_no') }}</th>
                                    <th>{{ __('supply_chain_trans.expiry_date') }}</th>
                                    <th>{{ __('supply_chain_trans.unit') }}</th>
                                    <th>{{ __('supply_chain_trans.system_qty') }}</th>
                                    <th>{{ __('supply_chain_trans.status') }}</th>
                                    <th>{{ __('supply_chain_trans.actual_qty') }}</th>
                                    <th>{{ __('supply_chain_trans.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم الثالث: اللجنة + المرفقات + الأزرار --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><strong>{{ __('supply_chain_trans.inventory_audit_details') }}</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('supply_chain_trans.committee_members') }}</label>
                            <select id="committee-select" name="committee_user_ids[]" class="form-control" multiple
                                style="width:100%"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('supply_chain_trans.attachments') }}</label>
                            <input type="file" name="attachments[]" form="audit-form" class="form-control" multiple>
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <button class="btn btn-success" id="btn-save" data-activate="0">
                            <i class="ri-save-2-line"></i> {{ __('supply_chain_trans.save') }}
                        </button>
                        <button class="btn btn-primary" id="btn-save-activate" data-activate="1">
                            <i class="ri-check-line"></i> {{ __('supply_chain_trans.save_and_activate_store') }}
                        </button>
                        <button class="btn btn-outline-danger" id="btn-activate-and-back">
                            <i class="ri-arrow-go-back-line"></i> {{ __('supply_chain_trans.back_and_activate_store') }}
                        </button>
                        <a href="{{ route('inventory_audits.index') }}"
                            class="btn btn-light">{{ __('supply_chain_trans.close') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: استعراض كل أرصدة المخزن --}}
    <div class="modal fade" id="balancesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('supply_chain_trans.store_balances') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table id="balances-table" class="table table-striped table-bordered w-100">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('supply_chain_trans.product') }}</th>
                                <th>{{ __('supply_chain_trans.product_code') }}</th>
                                <th>{{ __('supply_chain_trans.batch_no') }}</th>
                                <th>{{ __('supply_chain_trans.expiry_date') }}</th>
                                <th>{{ __('supply_chain_trans.unit') }}</th>
                                <th>{{ __('supply_chain_trans.system_qty') }}</th>
                                <th style="width:110px">{{ __('supply_chain_trans.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="text-muted small">{{ __('supply_chain_trans.tip_add_all_note') }}</div>
                    <div class="d-flex gap-2">
                        <button id="btn-add-all-balances-modal" class="btn btn-primary">
                            <i class="ri-play-list-add-line"></i> {{ __('supply_chain_trans.add_all') }}
                        </button>
                        <button class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('supply_chain_trans.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const STORE_ID = {{ $store->id }};
        let dt;                        // جدول البنود (Client-side)
        let balancesDT, balancesUnits; // مودال الأرصدة + كاش وحدات
        const draft = {};              // key => بيانات السطر
        let rowSeq = 0;                // عدّاد لضمان uniqueness

        function showToast(type, msg) {
            const id = 't' + Date.now() + Math.random().toString().slice(2);
            const html = `
                  <div id="${id}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                      <div class="toast-body">${msg}</div>
                      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                  </div>`;
            $('#toast-container').append(html);
            new bootstrap.Toast(document.getElementById(id), { delay: 3000 }).show();
        }
        function keyOf(pid, batch) { return String(pid) + '__' + String((batch || '').trim().toLowerCase()); }
        function escapeHtml(s) { return $('<div>').text(s || '').html(); }

        // === DataTable (Client-Side فارغ افتراضيًا) ===
        $(function () {
            dt = $('#items-table').DataTable({
                processing: true,
                deferRender: true,
                pageLength: 10,
                lengthChange: false,
                ordering: false,
                searching: false,
                info: true
            });

            dt.on('draw', function () {
                dt.column(0, { page: 'current' }).nodes().each((cell, i) => cell.innerHTML = (dt.page.info().start + i + 1));
            });
        });

        /* ===== Helpers: حفظ/استرجاع سطر من/إلى draft ===== */
        function collectRow($tr) {
            const pid = $tr.data('pid');
            const batch = ($tr.find('.batch-input').val() || '').trim();
            const currentKey = String($tr.attr('data-key') || '');
            const desiredKey = keyOf(pid, batch);
            const key = (batch === '' && /___\d+$/.test(currentKey)) ? currentKey : desiredKey;

            const name = $tr.find('td:eq(1)').text().trim();
            const code = $tr.find('td:eq(2)').text().trim();

            const sel = $tr.find('.unit-select :selected');
            const label = sel.data('label') || '-';
            const factor = parseFloat(sel.data('factor') || 1);

            const valStr = $tr.find('.unit-select').val() || '';
            const parts = valStr.split('|'); // [product_unit_id, unit_id]
            const puVal = parts[0] || '';
            const uId = parts[1] ? parseInt(parts[1], 10) : '';

            const sys = parseFloat($tr.find('.sys-qty').val() || 0);
            let act = parseFloat($tr.find('.act-qty').val() || 0);
            let status = $tr.find('input[type=radio]:checked').val() || 'match';

            if (status === 'match') {
                act = sys;
                $tr.find('.act-qty').val(sys.toFixed(4));
            }

            const diff = act - sys;
            const exp = $tr.find('.expiry-input').val() || '';

            return {
                key, product_id: pid, product_name: name, product_code: code,
                batch_no: batch, expiry_date: exp,
                product_unit_id: puVal, units_id: uId,
                unit_label: label, unit_factor: factor,
                system_quantity: sys, actual_quantity: act, difference_quantity: diff, status
            };
        }
        function saveRowDraft($tr) {
            const data = collectRow($tr);
            draft[data.key] = data;
        }

        // ===== دالة التحقق (blur + تغيير الحالة + قبل الحفظ)
        function validateStatusQuantity($tr, silent = false) {

            const status = $tr.find('input[type=radio]:checked').val() || 'match';
            const sys = parseFloat($tr.find('.sys-qty').val() || 0);
            let act = parseFloat($tr.find('.act-qty').val() || 0);

            if (act < 0) {
                if (!silent) showToast('warning', 'لا يُسمح بالقيم السالبة.');
                return false;
            }


            if (status === 'surplus' && !(act > sys)) {
                if (!silent) showToast('warning', 'عند اختيار "زيادة" يجب أن تكون الكمية الفعلية أكبر من الكمية بالنظام.');
                return false;
            }
            if (status === 'shortage' && !(act < sys)) {
                if (!silent) showToast('warning', 'عند اختيار "نقص" يجب أن تكون الكمية الفعلية أقل من الكمية بالنظام.');
                return false;
            }
            return true;
        }

        /* ===== بناء صف جديد ===== */
        function addManualRow(item, unitsMap) {
            rowSeq++;
            const baseKey = keyOf(item.product_id, item.batch_no || '');
            const tempKey = item.batch_no ? baseKey : (baseKey + '___' + rowSeq);
            const currentSeq = rowSeq;

            const newTr = $(`
                <tr class="manual-row" data-pid="${item.product_id}" data-base="${item.base_qty || 0}" data-key="${tempKey}">
                  <td class="text-muted"></td>
                  <td>${escapeHtml(item.product_name)}<input type="hidden" class="pid" value="${item.product_id}"></td>
                  <td>${escapeHtml(item.product_code || '')}</td>
                  <td><input class="form-control form-control-sm batch-input" value="${item.batch_no || ''}"></td>
                  <td><input type="date" class="form-control form-control-sm expiry-input" value="${item.expiry_date || ''}"></td>
                  <td><select class="form-select form-select-sm unit-select"></select><input type="hidden" class="pu-hidden"></td>
                  <td><input class="form-control form-control-sm sys-qty" value="0" readonly></td>
                  <td>
                    <div class="d-flex gap-2">
                      <label class="me-2"><input type="radio" name="mrow_${currentSeq}_status" value="surplus"> {{ __('supply_chain_trans.surplus') }}</label>
                      <label class="me-2"><input type="radio" name="mrow_${currentSeq}_status" value="shortage"> {{ __('supply_chain_trans.shortage') }}</label>
                      <label class="me-2"><input type="radio" name="mrow_${currentSeq}_status" value="match" checked> {{ __('supply_chain_trans.match') }}</label>
                    </div>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="number" min="0" step="0.0001" inputmode="decimal" class="form-control act-qty" value="0" disabled>
                      <span class="input-group-text unit-indicator">-</span>
                    </div>
                    <input type="hidden" class="diff-qty" value="0">
                  </td>
                  <td><button type="button" class="btn btn-sm btn-outline-danger del-row"><i class="ri-delete-bin-6-line"></i></button></td>
                </tr>
              `)[0];

            dt.row.add(newTr).draw(false);
            const $tr = $(newTr);

            function populate(unitsArr) {
                if (!unitsArr || unitsArr.length === 0) {
                    dt.row($tr).remove().draw(false);
                    showToast('warning', 'لا توجد وحدات معرفة لهذا الصنف');
                    return;
                }
                const sel = $tr.find('.unit-select').empty();
                unitsArr.forEach(u => {
                    sel.append(`<option value="${u.product_unit_id}|${u.unit_id}" data-factor="${u.factor}" data-label="${u.unit_name}">${u.unit_name}</option>`);
                });
                const factor = parseFloat(sel.find(':first').data('factor') || 1);
                const base = parseFloat($tr.data('base') || 0);
                const sysVal = (base / factor);

                $tr.find('.sys-qty').val(sysVal.toFixed(4));
                $tr.find('.pu-hidden').val((sel.val() || '').split('|')[0]);
                $tr.find('.unit-indicator').text(sel.find(':selected').data('label') || '-');

                $tr.find('.act-qty').prop('disabled', true).val(sysVal.toFixed(4));
                $tr.find('.diff-qty').val('0');

                saveRowDraft($tr);
            }

            const units = unitsMap ? (unitsMap[item.product_id] || []) : null;
            if (units) {
                populate(units);
            } else {
                $.get("{{ route('inventory_audits.ajax.product_units_bulk') }}", { ids: [item.product_id] }, function (map) {
                    populate(map[item.product_id] || []);
                });
            }
        }

        /* ===== أحداث الجدول ===== */
        $('#items-table tbody')
            // منع إدخال السالب ورموز غير لازمة أثناء الكتابة
            .on('keydown', '.act-qty', function (e) {
                // مفاتيح تحكم نسمح بها
                const ctrl = e.ctrlKey || e.metaKey;
                const allowed =
                    ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'].includes(e.key) ||
                    (ctrl && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase()));
                if (allowed) return;

                // منع السالب + والـ e/E (صيغة الأس)
                if (e.key === '-' || e.key === 'Minus' || e.key === '+' || e.key === 'e' || e.key === 'E' ||
                    e.keyCode === 189 || e.keyCode === 109 || e.keyCode === 187) {
                    e.preventDefault();
                    showToast('warning', 'لا يُسمح بالقيم السالبة.');
                }
            })

            // تنظيف اللصق
            .on('paste', '.act-qty', function (e) {
                const clip = (e.originalEvent || e).clipboardData.getData('text');
                if (/-/.test(clip)) {
                    e.preventDefault();
                    const sanitized = clip.replace(/-/g, '').replace(/[^\d.]/g, '');
                    document.execCommand('insertText', false, sanitized);
                    showToast('warning', 'لا يُسمح بالقيم السالبة.');
                }
            })

            // تنظيف مباشر أثناء الكتابة (إزالة أي محارف غير رقمية/نقاط زائدة/سالب)
            .on('input', '.act-qty', function () {
                let v = String($(this).val() || '');
                // إزالة أي شيء غير أرقام أو نقطة
                v = v.replace(/[^\d.]/g, '');
                // نقطة عشرية واحدة فقط
                const parts = v.split('.');
                if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
                $(this).val(v);
            })

            // تغيير الوحدة
            .on('change', '.unit-select', function () {
                const $tr = $(this).closest('tr');
                const factor = parseFloat($(this).find(':selected').data('factor') || 1);
                const label = $(this).find(':selected').data('label') || '-';
                const base = parseFloat($tr.data('base') || 0);

                const sysVal = (base / factor);
                $tr.find('.sys-qty').val(sysVal.toFixed(4)).trigger('change');
                $tr.find('.pu-hidden').val(($(this).val() || '').split('|')[0]);
                $tr.find('.unit-indicator').text(label);

                const status = $tr.find('input[type=radio]:checked').val() || 'match';
                if (status === 'match') {
                    $tr.find('.act-qty').prop('disabled', true).val(sysVal.toFixed(4));
                    $tr.find('.diff-qty').val('0');
                }

                saveRowDraft($tr);
            })

            // تحديث الفرق أثناء الكتابة فقط (لا نتحقق هنا)
            .on('input change', '.act-qty, .sys-qty', function () {
                const $tr = $(this).closest('tr');
                const status = $tr.find('input[type=radio]:checked').val() || 'match';

                let sys = parseFloat($tr.find('.sys-qty').val() || 0);
                let act = parseFloat($tr.find('.act-qty').val() || 0);

                if (status === 'match') {
                    act = sys;
                    $tr.find('.act-qty').prop('disabled', true).val(sys.toFixed(4));
                    $tr.find('.diff-qty').val('0');
                } else {
                    $tr.find('.diff-qty').val((act - sys).toFixed(4));
                }
                saveRowDraft($tr);
            })

            // خزّن القيمة القديمة عند بداية التركيز
            .on('focusin', '.act-qty', function () {
                $(this).data('prev', $(this).val());
            })

            // التحقق عند الخروج من الحقل
            .on('blur', '.act-qty', function () {
                const $tr = $(this).closest('tr');
                const ok = validateStatusQuantity($tr, false);
                if (!ok) {
                    const prev = $(this).data('prev');
                    const sys = $tr.find('.sys-qty').val();
                    $(this).val(prev ?? sys);
                    $tr.find('.diff-qty').val((parseFloat($(this).val()) - parseFloat(sys)).toFixed(4));
                }
                saveRowDraft($tr);
            })

            // تغيير الحالة: فعّل/عطّل + تحقّق فوري وإصلاح إن لزم
            .on('change', 'input[type=radio]', function () {
                const $tr = $(this).closest('tr');
                const statusVal = $(this).val();
                const $act = $tr.find('.act-qty');
                const sys = parseFloat($tr.find('.sys-qty').val() || 0);
                let act = parseFloat($act.val() || 0);

                if (statusVal === 'match') {
                    $act.prop('disabled', true).val(sys.toFixed(4));
                    $tr.find('.diff-qty').val('0');
                } else {
                    $act.prop('disabled', false);

                    // تحقّق فوري بعد تغيير الحالة
                    if (!validateStatusQuantity($tr, true)) {
                        // اضبط القيمة إلى حد آمن (= النظام) واطلب من المستخدم إدخال قيمة صحيحة
                        $act.val(sys.toFixed(4)).focus().select();
                        $tr.find('.diff-qty').val('0');
                        // أظهر تنبيه واضح
                        if (statusVal === 'surplus') {
                            showToast('warning', 'عند اختيار "زيادة" يجب إدخال كمية فعلية أكبر من الكمية بالنظام.');
                        } else if (statusVal === 'shortage') {
                            showToast('warning', 'عند اختيار "نقص" يجب إدخال كمية فعلية أقل من الكمية بالنظام.');
                        }
                    } else {
                        // لو كانت القيمة مناسبة بالفعل، حدّث الفرق فقط
                        act = parseFloat($act.val() || 0);
                        $tr.find('.diff-qty').val((act - sys).toFixed(4));
                    }
                }
                saveRowDraft($tr);
            })

            // تغيير الباتش + منع التكرار
            .on('change', '.batch-input', function () {
                const $tr = $(this).closest('tr');
                const pid = $tr.data('pid');
                const oldKey = String($tr.attr('data-key') || '');
                const val = ($(this).val() || '').trim().toLowerCase();
                const newKey = keyOf(pid, val);

                let duplicate = (newKey !== oldKey) && !!draft[newKey];

                if (!duplicate) {
                    $('#items-table tbody tr').each(function () {
                        if (this === $tr[0]) return;
                        const p = $(this).data('pid');
                        const b = ($(this).find('.batch-input').val() || '').trim().toLowerCase();
                        if (p == pid && b && b === val) { duplicate = true; return false; }
                    });
                }

                if (duplicate) {
                    showToast('warning', "{{ __('supply_chain_trans.batch_already_exists') }}");
                    if (oldKey.startsWith(pid + '__')) {
                        const prevBatch = oldKey.slice((pid + '__').length).replace(/^___\d+$/, '');
                        $(this).val(prevBatch);
                    } else {
                        $(this).val('');
                    }
                    return;
                }

                if (oldKey && draft[oldKey]) delete draft[oldKey];
                $tr.attr('data-key', newKey);
                saveRowDraft($tr);
            })

            // حذف صف
            .on('click', '.del-row', function () {
                const $tr = $(this).closest('tr');
                const k = $tr.attr('data-key');
                if (k && draft[k]) delete draft[k];
                dt.row($tr).remove().draw(false);
            });

        /* ===== مودال: أرصدة المخزن ===== */
        $('#btn-open-balances').on('click', function (e) {
            e.preventDefault();
            const $modalEl = document.getElementById('balancesModal');
            const $modal = new bootstrap.Modal($modalEl);

            if (!balancesDT) {
                balancesDT = $('#balances-table').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: "{{ route('inventory_audits.ajax.balances_paged', $store->id) }}",
                        type: 'GET'
                    },
                    pageLength: 10,
                    lengthChange: true,
                    lengthMenu: [10, 25, 50, 100],
                    searching: true,
                    order: [],
                    columns: [
                        { data: 'product_name', defaultContent: '' },
                        { data: 'product_code', defaultContent: '' },
                        { data: 'batch_no', defaultContent: '' },
                        { data: 'expiry_date', defaultContent: '' },
                        { data: null, defaultContent: '' }, // يملأ لاحقًا
                        { data: 'quantity_in_base_unit', defaultContent: 0 },
                        {
                            data: null, orderable: false, searchable: false,
                            render: () => `
                                  <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-primary btn-pick-balance" title="{{ __('supply_chain_trans.add') }}">
                                      <i class="ri-add-line"></i>
                                    </button>
                                  </div>`
                        }
                    ]
                });

                // تعبئة اسم الوحدة
                $('#balances-table').on('draw.dt', function () {
                    const api = balancesDT;
                    const data = api.rows({ page: 'current' }).data().toArray();
                    const ids = [...new Set(data.map(r => r.product_id))];
                    if (!ids.length) { balancesUnits = {}; return; }
                    $.get("{{ route('inventory_audits.ajax.product_units_bulk') }}", { ids }, function (map) {
                        balancesUnits = map || {};
                        api.rows({ page: 'current' }).every(function () {
                            const r = this.data();
                            const units = balancesUnits[r.product_id] || [];
                            const baseName = units.length ? units[0].unit_name : '-';
                            $(this.node()).find('td').eq(4).text(baseName);
                        });
                    });
                });

                // إضافة عنصر من الأرصدة
                $('#balances-table').on('click', '.btn-pick-balance', function () {
                    const row = balancesDT.row($(this).closest('tr')).data();
                    const k = keyOf(row.product_id, (row.batch_no || '').toLowerCase());
                    if (draft[k]) { showToast('info', "{{ __('supply_chain_trans.batch_already_exists') }}"); return; }

                    const unitsForProduct = (balancesUnits && balancesUnits[row.product_id]) ? balancesUnits[row.product_id] : [];
                    if (!unitsForProduct.length) {
                        showToast('warning', 'لا توجد وحدات معرفة لهذا الصنف');
                        return;
                    }

                    addManualRow({
                        product_id: row.product_id,
                        product_name: row.product_name || '',
                        product_code: row.product_code || '',
                        base_qty: parseFloat(row.quantity_in_base_unit || 0),
                        batch_no: row.batch_no || '',
                        expiry_date: row.expiry_date || ''
                    }, balancesUnits);

                    showToast('success', "{{ __('supply_chain_trans.added') }}");
                });

                // إضافة الكل (حتى 500)
                $('#btn-add-all-balances-modal').on('click', function () {
                    const term = balancesDT.search() || '';
                    $.get("{{ route('inventory_audits.ajax.balances_paged', $store->id) }}", { start: 0, length: 500, draw: 1, 'search[value]': term })
                        .then(resp => {
                            const rows = resp?.data || [];
                            if (!rows.length) { showToast('info', "{{ __('supply_chain_trans.no_records_found') }}"); return; }
                            const ids = [...new Set(rows.map(r => r.product_id))];
                            return $.get("{{ route('inventory_audits.ajax.product_units_bulk') }}", { ids })
                                .then(map => {
                                    let added = 0, skipped = 0, noUnit = 0;
                                    rows.forEach(r => {
                                        const units = (map && map[r.product_id]) ? map[r.product_id] : [];
                                        if (!units.length) { noUnit++; return; }

                                        const k = keyOf(r.product_id, (r.batch_no || '').toLowerCase());
                                        if (draft[k]) { skipped++; return; }

                                        addManualRow({
                                            product_id: r.product_id,
                                            product_name: r.product_name || '',
                                            product_code: r.product_code || '',
                                            base_qty: parseFloat(r.quantity_in_base_unit || 0),
                                            batch_no: r.batch_no || '',
                                            expiry_date: r.expiry_date || ''
                                        }, map);
                                        added++;
                                    });
                                    showToast('success', `{{ __('supply_chain_trans.added') }} ${added} {{ __('supply_chain_trans.from_balances') }}${skipped ? ' — {{ __("supply_chain_trans.skipped") }} ' + skipped : ''}${noUnit ? ' — بدون وحدات: ' + noUnit : ''}.`);
                                });
                        })
                        .catch(() => showToast('danger', '{{ __("supply_chain_trans.error_loading") }}'));
                });
            }

            $modal.show();
        });

        /* ===== إضافة يدوي: منتج ===== */
        function parseCodeAndName(txt) {
            const m = String(txt).match(/^\s*([^-\s]+)\s*-\s*(.+)$/);
            return m ? { code: m[1].trim(), name: m[2].trim() } : { code: '', name: String(txt).trim() };
        }

        $('#btn-add-product').off('click').on('click', function (e) {
            e.preventDefault();
            const pid = $('#product-select').val();
            if (!pid) { showToast('warning', "{{ __('supply_chain_trans.select_product_first') }}"); return; }

            const txt = $('#product-select').select2('data')[0]?.text || '';
            const { code, name } = parseCodeAndName(txt);

            addManualRow({
                product_id: pid,
                product_name: name,
                product_code: code,
                base_qty: 0,
                batch_no: '',
                expiry_date: ''
            });
        });

        /* ===== إضافة يدوي: مجموعة ===== */
        $('#btn-add-category').off('click').on('click', function (e) {
            e.preventDefault();
            const cid = $('#category-select').val();
            if (!cid) { showToast('warning', "{{ __('supply_chain_trans.select_category_first') }}"); return; }

            $.get("{{ route('inventory_audits.ajax.products_by_category', '__ID__') }}".replace('__ID__', cid), function (rows) {
                const ids = (rows || []).map(p => p.id);
                if (!ids.length) { showToast('info', "{{ __('supply_chain_trans.category_has_no_products') }}"); return; }

                $.get("{{ route('inventory_audits.ajax.balances_by_products', $store->id) }}", { ids }, function (balRows) {
                    const balancedIds = new Set((balRows || []).map(r => r.product_id));
                    const uniqIds = [...new Set(ids)];

                    $.get("{{ route('inventory_audits.ajax.product_units_bulk') }}", { ids: uniqIds }, function (map) {
                        let addedBalances = 0, skipped = 0, noUnit = 0;

                        (balRows || []).forEach(r => {
                            const units = (map && map[r.product_id]) ? map[r.product_id] : [];
                            if (!units.length) { noUnit++; return; }

                            const k = keyOf(r.product_id, (r.batch_no || '').toLowerCase());
                            if (draft[k]) { skipped++; return; }
                            addManualRow({
                                product_id: r.product_id,
                                product_name: r.product_name || '',
                                product_code: r.product_code || '',
                                base_qty: parseFloat(r.quantity_in_base_unit || 0),
                                batch_no: r.batch_no || '',
                                expiry_date: r.expiry_date || ''
                            }, map);
                            addedBalances++;
                        });

                        let addedEmpty = 0;
                        (rows || []).forEach(p => {
                            if (balancedIds.has(p.id)) return;
                            const units = (map && map[p.id]) ? map[p.id] : [];
                            if (!units.length) { noUnit++; return; }

                            const { code, name } = parseCodeAndName(p.text || '');
                            addManualRow({
                                product_id: p.id,
                                product_name: name,
                                product_code: code || p.code || '',
                                base_qty: 0,
                                batch_no: '',
                                expiry_date: ''
                            }, map);
                            addedEmpty++;
                        });

                        showToast('success', `{{ __('supply_chain_trans.added') }}: ${addedBalances} {{ __('supply_chain_trans.from_balances') }} + ${addedEmpty} {{ __('supply_chain_trans.without_balance') }}${skipped ? ' ({{ __("supply_chain_trans.skipped") }} ' + skipped + ')' : ''}${noUnit ? ' — بدون وحدات: ' + noUnit : ''}.`);
                    });
                });
            });
        });

        /* ===== حفظ ===== */
        function submitForm(activate) {
            // فحص نهائي قبل الحفظ
            let anyInvalid = false, firstBad = null;
            $('#items-table tbody tr').each(function () {
                const $tr = $(this);
                const status = $tr.find('input[type=radio]:checked').val() || 'match';
                if (status !== 'match' && !validateStatusQuantity($tr, true)) {
                    anyInvalid = true;
                    firstBad = firstBad || $tr;
                }
            });
            if (anyInvalid) {
                showToast('danger', 'هناك بنود بها تعارض بين الحالة والكمية الفعلية. رجاءً صحّحها أولاً.');
                if (firstBad) firstBad.find('.act-qty').focus();
                return;
            }

            const fd = new FormData(document.getElementById('audit-form'));
            fd.append('activate_store', activate ? 1 : 0);

            // أعضاء اللجنة
            const committeeIds = $('#committee-select').val() || [];
            committeeIds.forEach((id, i) => fd.append(`committee_user_ids[${i}]`, id));

            // البنود
            let idx = 0;
            for (const k in draft) {
                const d = draft[k];
                const sys = parseFloat(d.system_quantity || 0);
                const act = (d.status === 'match') ? sys : parseFloat(d.actual_quantity || 0);
                const diff = act - sys;

                idx++;
                fd.append(`items[${idx}][product_id]`, d.product_id);
                fd.append(`items[${idx}][product_unit_id]`, d.product_unit_id || '');
                fd.append(`items[${idx}][units_id]`, d.units_id || '');
                fd.append(`items[${idx}][batch_no]`, d.batch_no || '');
                fd.append(`items[${idx}][expiry_date]`, d.expiry_date || '');
                fd.append(`items[${idx}][system_quantity]`, sys);
                fd.append(`items[${idx}][actual_quantity]`, act);
                fd.append(`items[${idx}][difference_quantity]`, diff);
                fd.append(`items[${idx}][status]`, d.status || 'match');
            }

            if (idx === 0) {
                showToast('warning', "{{ __('supply_chain_trans.add_at_least_one_item') }}");
                return;
            }

            $.ajax({
                url: "{{ route('inventory_audits.store') }}",
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: () => {
                    showToast('success', "{{ __('supply_chain_trans.saved_successfully') }}");
                    setTimeout(() => window.location.href = "{{ route('inventory_audits.index') }}", 600);
                },
                error: xhr => {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error';
                    showToast('danger', msg);
                }
            });
        }

        // أزرار الحفظ
        $('#btn-save').on('click', () => submitForm(0));
        $('#btn-save-activate').on('click', () => submitForm(1));

        /* ===== زر الرجوع وتفعيل المخزن ===== */
        $('#btn-activate-and-back').on('click', function () {
            if (!confirm("{{ __('supply_chain_trans.confirm_activate_store_and_back') ?? 'سيتم تفعيل المخزن والرجوع، هل أنت متأكد؟' }}")) return;
            $.post("{{ route('inventory_audits.reactivate_store') }}", { store_id: STORE_ID })
                .done(resp => {
                    showToast('success', "{{ __('supply_chain_trans.store_activated') ?? 'تم تفعيل المخزن' }}");
                    const url = resp?.redirect || "{{ route('inventory_audits.index') }}";
                    setTimeout(() => window.location.href = url, 400);
                })
                .fail(() => showToast('danger', "{{ __('supply_chain_trans.error_loading') }}"));
        });

        /* ===== Select2 ===== */
        $('#product-select').select2({
            placeholder: "{{ __('supply_chain_trans.search') }}",
            ajax: { url: "{{ route('inventory_audits.ajax.products') }}", data: p => ({ q: p.term }), processResults: d => ({ results: d }) }
        });
        $('#category-select').select2({
            placeholder: "{{ __('supply_chain_trans.search') }}",
            ajax: { url: "{{ route('inventory_audits.ajax.categories') }}", data: p => ({ q: p.term }), processResults: d => ({ results: d }) }
        });
        $('#committee-select').select2({
            placeholder: "{{ __('supply_chain_trans.search') }}",
            ajax: { url: "{{ route('inventory_audits.ajax.employees') }}", data: p => ({ q: p.term }), processResults: d => ({ results: d }) }
        });
    </script>
@endsection
