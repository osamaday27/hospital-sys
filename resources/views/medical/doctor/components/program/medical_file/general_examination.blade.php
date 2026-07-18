<div class="tab-pane fade" id="general_examination" role="tabpanel">
    <div class="row">
        <!-- Section for Adding Data -->
        <div class="col-md-6">
            <div class="card border-primary shadow-lg rounded">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="mdi mdi-plus-circle me-2"></i>
                    <span>{{ trans('doctor_trans.add_general_examination') }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ trans('doctor_trans.general_appearance') }}</label>
                        <input type="text" class="form-control" name="general_appearance"
                               placeholder="{{ trans('doctor_trans.enter_general_appearance') }}">
                    </div>

                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{ trans('doctor_trans.system') }}</th>
                                <th>Insignificant</th>
                                <th>{{ trans('doctor_trans.comments') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="mdi mdi-human-head text-info me-1"></i> {{ trans('doctor_trans.head_neck') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckHeadNeck" name="SwitchCheckHeadNeck" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_head_neck"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-lungs text-danger me-1"></i> {{ trans('doctor_trans.chest') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckChest" name="SwitchCheckChest" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_chest"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-stomach text-warning me-1"></i> {{ trans('doctor_trans.abdomen') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckAbdomen" name="SwitchCheckAbdomen" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_abdomen"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-brain text-primary me-1"></i> {{ trans('doctor_trans.cns') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckCNS" name="SwitchCheckCNS" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_cns"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-heart-pulse text-danger me-1"></i> {{ trans('doctor_trans.heart') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckHeart" name="SwitchCheckHeart" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_heart"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-food-apple text-success me-1"></i> {{ trans('doctor_trans.git') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckGIT" name="SwitchCheckGIT" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_git"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-water text-primary me-1"></i> {{ trans('doctor_trans.edema') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckEdema" name="SwitchCheckEdema" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_edema"></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-water text-primary me-1"></i> {{ trans('doctor_trans.other') }}</td>
                                <td>
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheckOther" name="SwitchCheckOther" checked>
                                    </div>
                                </td>
                                <td><input type="text" class="form-control" name="comment_other"></td>
                            </tr>
                        </tbody>
                    </table>

                    <button id="saveGeneralExamination" class="btn btn-success w-100 mt-3 fw-bold">
                        <i class="mdi mdi-content-save me-1"></i> {{ trans('doctor_trans.save') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Section for Displaying Data -->
        <div class="col-md-6">
            <div class="card border-secondary shadow-lg rounded">
                <div class="card-header bg-secondary text-white d-flex align-items-center">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    <span>{{ trans('doctor_trans.previous_examinations') }}</span>
                    <div class="ms-auto">
                        <!-- زر تحديث احتياطي -->
                        <button type="button" id="btnRefreshGeneralExam" class="btn btn-light btn-sm">
                            <i class="mdi mdi-refresh"></i> {{ trans('doctor_trans.refresh') ?? 'تحديث' }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap examination-table"
                               id="general_examinationsTable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ trans('doctor_trans.system') }}</th>
                                    <th>{{ trans('doctor_trans.date') }}</th>
                                    <th>{{ trans('doctor_trans.createby') }}</th>
                                </tr>
                            </thead>
                            <tbody id="previous-examinations-body">
                                <!-- Dynamic Data Will be Loaded Here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Shim عام + دوال مساعدة (ضعه كما هو) -->
<script>
  // ممكن يبقى موجود من قبل، فبنحافظ عليه
  window._generalExamPendingReload = window._generalExamPendingReload || false;

  // دالة تحديث موحّدة (تعمل init لو محتاج)
  function reloadGeneralExaminations(forceInit){
    const sel = '#general_examinationsTable';
    const ready = window.jQuery && jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.isDataTable && jQuery.fn.dataTable.isDataTable(sel);

    if (!ready && forceInit) { try { initGeneralExamTable(); } catch(e) {} }

    const readyNow = window.jQuery && jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.isDataTable && jQuery.fn.dataTable.isDataTable(sel);
    if (readyNow) {
        window.generalExamDT = window.generalExamDT || jQuery(sel).DataTable();
        window.generalExamDT.ajax.reload(null, false); // لا ترجع لأول صفحة
        // alias للتوافق مع أي أكواد قديمة بتستخدم table
        window.table = window.generalExamDT;
        window._generalExamPendingReload = false;
    } else {
        window._generalExamPendingReload = true;
    }
  }

  // Shim لاستدعاءات قديمة: table.ajax.reload(...)
  if (typeof window.table === 'undefined') {
    window.table = { ajax: { reload: function(){ reloadGeneralExaminations(true); } } };
  } else {
    window.table.ajax = window.table.ajax || {};
    if (typeof window.table.ajax.reload !== 'function') {
      window.table.ajax.reload = function(){ reloadGeneralExaminations(true); };
    }
  }

  // دالة debounce عامة (نستخدمها للبحث)
  function debounce(fn, delay){ let t; return function(){ clearTimeout(t); t=setTimeout(()=>fn.apply(this, arguments), delay); }; }

  // لو فيه طلب تحديث قبل التهيئة، نفّذه بعد تحميل DOM
  document.addEventListener('DOMContentLoaded', function(){
    if (window._generalExamPendingReload) reloadGeneralExaminations(true);
  });
</script>

<!-- 🔄 تهيئة DataTable + ربط البحث التلقائي + زر التحديث -->
<script>
  function initGeneralExamTable() {
    const selector = '#general_examinationsTable';

    if ($.fn.dataTable.isDataTable(selector)) {
      window.generalExamDT = $(selector).DataTable();
      window.table = window.generalExamDT; // alias
      return;
    }

    window.generalExamDT = $(selector).DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      stateSave: true,          // يحفظ حالة البحث والصفحة والطول
      searchDelay: 400,         // يقلل ضغط الطلبات أثناء الكتابة
      order: [[1, 'desc']],     // التاريخ نزولًا
      language: {
        search: "🔍 بحث:",
        lengthMenu: "عرض _MENU_ سجل لكل صفحة",
        info: "عرض _START_ إلى _END_ من _TOTAL_ سجل",
        infoEmpty: "لا توجد بيانات متاحة",
        infoFiltered: "(تمت تصفيتها من إجمالي _MAX_ سجلات)",
        paginate: { first: "الأول", last: "الأخير", next: "التالي", previous: "السابق" }
      },
      ajax: "{{ route('general_examinations.index', $patient->id) }}",
      columns: [
        { data: 'systems_review',        name: 'systems_review' },
        { data: 'formatted_created_at',  name: 'formatted_created_at' },
        { data: 'user_name',             name: 'user_name' }
      ]
    });

    window.table = window.generalExamDT; // alias
  }

  function bindGeneralExamSearchAutoReload() {
    const selector = '#general_examinationsTable';
    const $filterInput = $(`${selector}_filter input[type="search"]`);
    if (!$filterInput.length) return;

    // الغِ أي ربط قديم بنفس الـ namespace
    $filterInput.off('input.generalExamSearch');

    // كتابة → انتظار 400ms → draw من غير رجوع لأول صفحة
    $filterInput.on('input.generalExamSearch', debounce(function(){
      const val = $(this).val();
      if (window.generalExamDT) {
        window.generalExamDT.search(val).draw(false);
      }
    }, 400));
  }

  $(document).ready(function () {
    initGeneralExamTable();
    bindGeneralExamSearchAutoReload();

    // لو الجدول داخل تبويب، ظبط الأعمدة عند الإظهار
    $('a[data-bs-toggle="tab"][href="#general_examination"]').on('shown.bs.tab', function () {
      if (window.generalExamDT) {
        window.generalExamDT.columns.adjust().responsive.recalc();
        bindGeneralExamSearchAutoReload();
      }
    });

    // بعد أي draw، تأكد من ربط حقل البحث
    $('#general_examinationsTable').on('draw.dt', function () {
      bindGeneralExamSearchAutoReload();
    });

    // زر التحديث اليدوي
    $(document).on('click', '#btnRefreshGeneralExam', function(){
      reloadGeneralExaminations(true);
    });
  });
</script>

<!-- 💾 حفظ + تحديث الجدول بعد الحفظ -->
<script>
  $(document).ready(function () {
    $('#saveGeneralExamination').on('click', function(e) {
      e.preventDefault();

      const $btn = $(this);
      const oldHtml = $btn.html();
      $btn.prop('disabled', true)
          .html('<i class="mdi mdi-loading mdi-spin me-1"></i>{{ trans('doctor_trans.save') }}');

      const formData = {
        patient_id: $('input[name="id_patient"]').val(),
        visit_id: $('input[name="id_visit"]').val(),
        general_appearance: $('input[name="general_appearance"]').val(),
        head_neck:  $('#SwitchCheckHeadNeck').is(':checked') ? 1 : 0,
        comment_head_neck: $('input[name="comment_head_neck"]').val(),
        chest:     $('#SwitchCheckChest').is(':checked') ? 1 : 0,
        comment_chest: $('input[name="comment_chest"]').val(),
        abdomen:   $('#SwitchCheckAbdomen').is(':checked') ? 1 : 0,
        comment_abdomen: $('input[name="comment_abdomen"]').val(),
        cns:       $('#SwitchCheckCNS').is(':checked') ? 1 : 0,
        comment_cns: $('input[name="comment_cns"]').val(),
        heart:     $('#SwitchCheckHeart').is(':checked') ? 1 : 0,
        comment_heart: $('input[name="comment_heart"]').val(),
        git:       $('#SwitchCheckGIT').is(':checked') ? 1 : 0,
        comment_git: $('input[name="comment_git"]').val(),
        edema:     $('#SwitchCheckEdema').is(':checked') ? 1 : 0,
        comment_edema: $('input[name="comment_edema"]').val(),
        other:     $('#SwitchCheckOther').is(':checked') ? 1 : 0,
        comment_other: $('input[name="comment_other"]').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
      };

      $.ajax({
        url: "{{ route('general_examinations.store') }}",
        type: "POST",
        data: formData,
        headers: { 'X-CSRF-TOKEN': formData._token },
        success: function(resp) {
          Swal.fire({ icon: 'success', title: 'تم الحفظ', text: resp.message || 'تمت الإضافة بنجاح' });

          // ✅ حدّث الجدول فورًا (هيعمل init لو لسه متعملش)
          reloadGeneralExaminations(true);

          // (اختياري) تفريغ الحقول
          $('input[name="general_appearance"]').val('');
          $('input[name^="comment_"]').val('');
          $('#SwitchCheckHeadNeck,#SwitchCheckChest,#SwitchCheckAbdomen,#SwitchCheckCNS,#SwitchCheckHeart,#SwitchCheckGIT,#SwitchCheckEdema,#SwitchCheckOther').prop('checked', true);
        },
        error: function(xhr) {
          let msg = 'حدث خطأ غير متوقع';
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            msg = Object.values(xhr.responseJSON.errors).map(v => v[0]).join('\n');
          }
          Swal.fire({ icon: 'error', title: 'خطأ في الإدخال', text: msg });
        },
        complete: function () {
          $btn.prop('disabled', false).html(oldHtml);
        }
      });
    });
  });
</script>
