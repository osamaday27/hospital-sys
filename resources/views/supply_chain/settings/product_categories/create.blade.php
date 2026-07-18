@extends('layouts.master_table')

@section('title')
    {{ trans('supply_chain_trans.create_product_categories') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BreadCrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('product_categories.index') }}">{{ trans('supply_chain_trans.product_categories') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('supply_chain_trans.create_product_categories') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('supply_chain_trans.create_product_categories') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('product_categories.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- الاسم بالعربية -->
                            <div class="col-md-6 mb-3">
                                <label for="name_ar" class="form-label">{{ trans('inpatient_trans.name_ar') }}</label>
                                <input type="text" id="name_ar" name="name_ar" class="form-control" required>
                            </div>

                            <!-- الاسم بالإنجليزية -->
                            <div class="col-md-6 mb-3">
                                <label for="name_en" class="form-label">{{ trans('inpatient_trans.name_en') }}</label>
                                <input type="text" id="name_en" name="name_en" class="form-control" required>
                            </div>
                            <!-- الحالة -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ trans('inpatient_trans.status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="1">{{ trans('call_center_trans.active') }}</option>
                                    <option value="0">{{ trans('call_center_trans.inactive') }}</option>
                                </select>
                            </div>

                            <!-- نوع المجموعة -->
                            <div class="col-md-6 mb-3">
                                <label for="group_type"
                                    class="form-label">{{ trans('supply_chain_trans.group_type') }}</label>
                                <select id="group_type" name="group_type" class="form-control" required>
                                    <option value="main">{{ trans('supply_chain_trans.main_group') }}</option>
                                    <option value="sub">{{ trans('supply_chain_trans.sub_group') }}</option>
                                </select>
                            </div>

                            <!-- في حالة اختيار "فرعي": قائمة اختيار المجموعة الرئيسية -->
                            <div class="col-md-6 mb-3" id="main_group_section" style="display: none;">
                                <label for="main_group_id"
                                    class="form-label">{{ trans('supply_chain_trans.main_category') }}</label>
                                <select id="main_group_id" name="main_group_id" class="form-control">
                                    <option value="">{{ trans('supply_chain_trans.select_main_category') }}</option>
                                    <!-- يفترض جلب المجموعات الرئيسية من المتغير $mainCategories -->
                                    @foreach ($mainCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- قائمة اختيار الفرع باستخدام checkboxes -->
                            <div class="col-md-6 mb-3" id="branch_sort_section">
                                <label class="form-label">{{ trans('supply_chain_trans.branch') }}</label>
                                <div>
                                    @foreach ($branches as $branch)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="branch_{{ $branch->id }}"
                                                name="branch[]" value="{{ $branch->id }}">
                                            <label class="form-check-label"
                                                for="branch_{{ $branch->id }}">{{ $branch->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- رسالة الخطأ -->
                                <div id="branch-error" class="text-danger mt-2" style="display: none;">
                                    {{ trans('supply_chain_trans.select_at_least_one_branch') }}
                                </div>
                            </div>


                            <!-- حقل الترتيب -->
                            <div class="col-md-6 mb-3" id="sort_section">
                                <label for="sort" class="form-label">{{ trans('supply_chain_trans.sort') }}</label>
                                <input type="number" id="sort" name="sort" class="form-control"
                                    placeholder="{{ trans('supply_chain_trans.sort') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('product_categories.index') }}" class="btn btn-secondary">
                                <i class="ri-close-line"></i> {{ trans('call_center_trans.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> {{ trans('call_center_trans.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- جافاسكربت لتبديل عرض قائمة المجموعة الرئيسية فقط دون التأثير على باقي الحقول -->
    <script>
        $(document).ready(function() {
            function toggleGroupFields() {
                var groupType = $('#group_type').val();
                if (groupType === 'sub') {
                    $('#main_group_section').show();
                } else {
                    $('#main_group_section').hide();
                }
            }

            toggleGroupFields();

            $('#group_type').on('change', function() {
                toggleGroupFields();
            });

            // التحقق من اختيار فرع واحد على الأقل
            $('form').on('submit', function(e) {
                let isBranchChecked = $('input[name="branch[]"]:checked').length > 0;
                if (!isBranchChecked) {
                    e.preventDefault();
                    $('#branch-error').show(); // عرض الرسالة
                } else {
                    $('#branch-error').hide(); // إخفاؤها في حال تم التصحيح
                }
            });
        });
    </script>

@endsection
