<?php

return [
    'title' => 'الأصول',
    'assets_settings' => 'اعدادات الأصول',
    'general' => 'الاعدادات العامة',
    'categories' => 'فئات الأصول',
    'brands' => 'الماركات',
    'models' => 'الموديلات',
    'locations' => 'المواقع',
    'general_settings' => 'الإعدادات العامة',
    'create' => 'إنشاء',
    'edit' => 'تعديل',
    'save' => 'حفظ',
    'search' => 'بحث…',
    'code' => 'الكود',
    'name' => 'الاسم',
    'name_ar' => 'الاسم (عربي)',
    'name_en' => 'الاسم (إنجليزي)',
    'parent' => 'الأب',
    'status' => 'الحالة',
    'actions' => 'الإجراءات',
    'life_months' => 'العمر بالاشهر',
    'active' => 'نشط',
    'code_prefix' => 'بادئة الأكواد',
    'qr_size' => 'حجم QR',
    'qr_margin' => 'هامش QR',
    'enable_rfid' => 'تفعيل RFID',
    'qr_disk' => 'قرص التخزين',
    'qr_path' => 'مسار حفظ QR',
    'stay_here' => 'البقاء',
    'back_to_list' => 'العودة للقائمة',
    'default_settings'  => 'الإعدادات الافتراضية',
    'dep_method'        => 'طريقة الإهلاك',
    'dep_rate'          => 'نسبة الإهلاك',
    'dep_method_values' => [
        'SL'  => 'القسط الثابت',
        'DDB' => 'مضاعف القسط المتناقص',
        'UOP' => 'وحدات الإنتاج',
    ],
    // الحقول العامة
    'choose'            => 'اختر…',
    'choose_optional'   => 'اختياري',

    // طرق الإهلاك (استخدم نفس هذه المفاتيح في القوائم والعرض)
    'dep_method_SL'  => 'القسط الثابت (SL)',
    'dep_method_DDB' => 'الميزان المتناقص (DDB)',
    'dep_method_UOP' => 'وحدات الإنتاج (UOP)',

    'inactive' => 'متوقف',
    'none' => 'لا يوجد',

    // Newly added keys for unified UI
    'show' => 'عرض',
    'delete' => 'حذف',
    'cancel' => 'إلغاء',
    'close' => 'إغلاق',
    'loading' => '... جاري التحميل ...',
    'load_error' => 'حدث خطأ أثناء التحميل',
    'no_content' => 'لا توجد بيانات للعرض',
    'deleted_success' => 'تم الحذف بنجاح',
    'delete_failed' => 'فشل الحذف',
    'delete_confirm' => 'تأكيد الحذف',

    // Fields
    'type' => 'النوع',

    // Delete warnings
    'location_delete_warning' => 'سيتم حذف الموقع نهائياً. قد يؤثر على السجلات المرتبطة.',
    'model_delete_warning' => 'سيتم حذف الموديل نهائياً. قد يؤثر على السجلات المرتبطة.',

    'are_you_sure_delete'   => 'هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذه العملية.',
    'deleting'              => 'جارٍ الحذف...',


    // ==== تحذيرات خاصة بالكيانات ====
    'brand_delete_warning'     => 'سيتم حذف الماركة المحددة. هل أنت متأكد؟',
    'category_delete_warning'  => 'سيتم حذف الفئة وجميع المعلومات المرتبطة بها (إن وُجدت). هل أنت متأكد؟',

    // ==== حالات منع الحذف ====
    'cannot_delete_has_children'   => 'لا يمكن الحذف لوجود عناصر فرعية.',
    'cannot_delete_in_use'         => 'لا يمكن الحذف لأن السجل مستخدم في عمليات أخرى.',
    'cannot_delete_default'        => 'لا يمكن حذف السجل الافتراضي.',

    // ==== حذف نهائي / سلة المحذوفات (اختياري) ====
    'force_delete'           => 'حذف نهائي',
    'deleted_permanently'    => 'تم الحذف نهائيًا.',
    'restore'                => 'استعادة',
    'trash'                  => 'سلة المحذوفات',

    // ==== حذف جماعي (اختياري) ====
    'bulk_delete'            => 'حذف جماعي',
    'bulk_delete_confirm'    => 'تأكيد الحذف الجماعي',
    'select_records_to_delete' => 'يرجى اختيار سجلات للحذف.',
    'bulk_deleted_success'   => 'تم حذف السجلات المحددة.',
    'bulk_delete_failed'     => 'تعذر حذف بعض السجلات.',
    'related_records_count'  => 'عدد السجلات المرتبطة: :count',

    // كيانات
    'items' => 'الأصول',
    'documents' => 'الوثائق',
    'asset' => 'الأصل',

    // عامة

    'print' => 'طباعة',
    'assignments' => 'الاسنادات',



    'notes' => 'ملاحظات',
    'all_suppliers' => 'جميع الموردين',

    'print_card' => 'طباعة بطاقة',
    'assets_programs' => 'برامج الاصول',

    // حقول الأصول

    'serial_no' => 'الرقم التسلسلي',
    'category' => 'الفئة',
    'brand' => 'الماركة',
    'model' => 'الموديل',
    'location' => 'الموقع',
    'store' => 'المخزن',
    'supplier' => 'المورد',
    'purchase_date' => 'تاريخ الشراء',
    'cost' => 'التكلفة',
    'status_in_service' => 'قيد الخدمة',
    'status_maintenance' => 'صيانة',
    'status_retired' => 'محال للتقاعد',
    'status_scrapped' => 'خردة',
    'qr_code' => 'رمز QR',
    'qr_not_available' => 'لا يوجد QR محفوظ',

    // الوثائق
    'doc_no' => 'رقم الوثيقة',
    'doc_date' => 'تاريخ الوثيقة',
    'file' => 'الملف',
    'download' => 'تحميل',
    'doc_invoice' => 'فاتورة',
    'doc_warranty' => 'ضمان',
    'doc_other' => 'أخرى',
    'all_assets' => 'كل الأصول',
    'add_document' => 'إضافة وثيقة',

    // رسائل
    'saved' => 'تم الحفظ بنجاح.',
    'saved_stay' => 'تم الحفظ، يمكنك إدخال سجل جديد.',
    'updated' => 'تم التحديث.',
    'updated_stay' => 'تم التحديث وبقيت في نفس الصفحة.',
    'no_qr'          => 'لا يوجد QR',
    'not_available'  => 'غير متاح',
    'asstitle'  => 'اسنادات الاصول',


    'search_placeholder' => 'ابحث',
    'assignable_type' => 'نوع الجهة',
    'type_employee' => 'موظف',
    'type_department' => 'قسم',
    'type_store' => 'مخزن',
    'type_supplier' => 'مورد',
    'type_other' => 'أخرى',
    'employee' => 'الموظف',
    'department' => 'القسم',
    'assignee' => 'الجهة المُسند إليها',
    'assignable_name' => 'اسم الجهة (اختياري)',
    'assignable_name_ph' => 'اكتب اسماً مخصصاً إن رغبت',
    'assignable_name_hint' => 'يُعرض بجانب اسم الجهة الفعلي.',
    'assigned_at' => 'تاريخ الإسناد',
    'due_back_at' => 'تاريخ الاسترجاع المتوقع',
    'returned_at' => 'تاريخ الإرجاع',
    'status_assigned' => 'مسند',
    'status_returned' => 'مُعاد',
    'handover' => 'مستند التسليم / الارجاع',
    'validation_errors' => 'حدث خطأ في الاسناد / الارجاع',


    'save_stay' => 'حفظ – البقاء هنا',
    'save_back' => 'حفظ – الرجوع للقائمة',
    'update_stay' => 'تحديث – البقاء هنا',
    'update_back' => 'تحديث – الرجوع للقائمة',

    'asset_already_assigned' => 'هذا الأصل لديه إسناد فعّال ولم يتم إرجاعه بعد.',
    'assignment'  => 'إسناد',
    'create_assignment' => 'إنشاء إسناد',
    'edit_assignment'   => 'تعديل الإسناد',


    'return'   => 'إرجاع',
    'confirm_return_title' => 'تأكيد إرجاع الأصل',
    'confirm_return_text'  => 'هل تريد إرجاع الأصل إلى نفس الجهة المسند إليها؟',
    'confirm' => 'تأكيد',

    'returned_success' => 'تم الإرجاع بنجاح',
    'action_failed'  => 'تعذر تنفيذ العملية',


    // أخطاء منطق/تحقق
    'err_already_assigned' => 'لا يمكن إسناد هذا الأصل لأنه مُسند حاليًا.',
    'err_not_assigned'     => 'هذا السجل ليس في حالة إسناد حالية.',

    // أنواع الجهات (للاستخدام عند الحاجة)
    'assignee_type_employee'  => 'موظف',
    'assignee_type_department' => 'قسم',
    'assignee_type_store'     => 'مخزن',
    'assignee_type_supplier'  => 'مورد',
    'assignee_type_other'     => 'أخرى',


    // الإسناد
    'assignee_fmt' => ':type — :name',

    'err_already_returned' => 'تم تسجيل إرجاع لهذا الإسناد مسبقًا.',
    'err_asset_already_assigned' => 'الأصل مسند بالفعل ولم يتم إرجاعه.',
    'err_assignable_id_required' => 'يجب اختيار الجهة.',
    'err_assignable_name_required' => 'يرجى إدخال اسم الجهة عند اختيار "أخرى".',

    // المودال
    'confirm_return_msg' => 'هل تريد تسجيل إرجاع هذا الأصل الآن؟',
    'return_btn' => 'ارجاع',

    'qty' => 'الكمية',
    'return_qty' => 'كمية الإرجاع',
    'err_qty_exceeds_available' => 'الكمية المطلوبة تتجاوز المتاح (:available).',
    'err_nothing_to_return' => 'لا توجد كمية متبقية للإرجاع.',
    'err_return_exceeds_outstanding' => 'كمية الإرجاع تتجاوز المتبقي (:remaining).',
    'reload' => 'تحديث',
    'err_asset_not_in_service' => 'لا يمكن إسناد هذا الأصل لأنه غير قيد الخدمة.',
    'remaining' => 'المتبقي',
    'return_qty_invalid' => 'كمية الإرجاع غير صحيحة أو تتجاوز المتبقي.',

    //maintenance
    'maintenance_title' => 'الصيانة',
    'maintenance' => [

        // شاشة الفهرس (index)
        'index_title' => 'صيانة الأصول',
        'add_btn'     => 'إضافة صيانة',

        'filters' => [
            'asset_label'       => 'الأصل',
            'status_label'      => 'الحالة',
            'from_label'        => 'من',
            'to_label'          => 'إلى',
            'apply_btn'         => 'تطبيق',
            'asset_placeholder' => 'اختر أصل (اختياري)',
            'all'               => 'الكل',
        ],

        'table' => [
            'code'        => 'الكود',
            'asset'       => 'الأصل',
            'type'        => 'النوع',
            'status'      => 'الحالة',
            'priority'    => 'الأولوية',
            'reported_at' => 'تاريخ البلاغ',
            'total'       => 'الإجمالي',
            'actions'     => 'إجراءات',
        ],

        // القوائم المنسدلة (للبادجز والنماذج)
        'types' => [
            'corrective' => 'تصحيحية',
            'preventive' => 'وقائية',
            'inspection' => 'تفقد/فحص',
        ],
        'statuses' => [
            'open'        => 'مفتوحة',
            'in_progress' => 'جارية',
            'done'        => 'منتهية',
            'cancelled'   => 'ملغاة',
        ],
        'priorities' => [
            'low'    => 'منخفضة',
            'medium' => 'متوسطة',
            'high'   => 'مرتفعة',
        ],

        // أزرار عامة + تأكيدات
        'buttons' => [
            'view'   => 'عرض',
            'edit'   => 'تعديل',
            'delete' => 'حذف',
            'back'   => 'رجوع',
            'save'   => 'حفظ',
        ],
        'confirm' => [
            'delete'       => 'حذف السجل؟',
            'title'       => 'تأكيد الحذف',
            'delete_named' => 'هل تريد حذف السجل :code؟',
            'cancel'      => 'إلغاء',
            'yes_delete'  => 'نعم، حذف',
        ],

        // رسائل نجاح (Flash)
        'flash' => [
            'created' => 'تم إنشاء سجل الصيانة بنجاح',
            'updated' => 'تم تحديث سجل الصيانة بنجاح',
            'deleted' => 'تم حذف سجل الصيانة بنجاح',
        ],

        // رسائل تحقق/أخطاء
        'validation' => [
            'cannot_close_done_without_cost' => 'لا يمكن إغلاق الصيانة بالحالة "منتهية" بدون بنود وتكلفة أكبر من صفر.',
        ],

        // شاشة العرض (show)
        'show' => [
            'title'   => 'تفاصيل صيانة أصل',
            'heading' => 'تفاصيل الصيانة: :code',
            'labels' => [
                'asset'       => 'الأصل',
                'type'        => 'النوع',
                'status'      => 'الحالة',
                'priority'    => 'الأولوية',
                'total'       => 'الإجمالي',
                'summary'     => 'ملخص',
                'description' => 'الوصف',
            ],
        ],

        // شاشة التعديل (edit)
        'edit' => [
            'title'   => 'تعديل صيانة أصل',
            'heading' => 'تعديل صيانة',
            'placeholders' => [
                'asset'  => 'اختر أصل',
                'vendor' => 'مزود (اختياري)',
            ],
        ],

        // شاشة الإضافة (create)
        'create' => [
            'title'   => 'إضافة صيانة أصل',
            'heading' => 'إضافة صيانة',
        ],

        // حقول النموذج العامة
        'form' => [
            'labels' => [
                'asset'         => 'الأصل',
                'type'          => 'النوع',
                'status'        => 'الحالة',
                'priority'      => 'الأولوية',
                'reported_at'   => 'تاريخ البلاغ',
                'summary'       => 'ملخص',
                'description'   => 'الوصف',
                'started_at'    => 'تاريخ البدء',
                'finished_at'   => 'تاريخ الانتهاء',
                'next_due_date' => 'الاستحقاق القادم',
                'vendor'        => 'المزوّد',
                'attachment'    => 'مرفق (اختياري)',
            ],
            'placeholders' => [
                'asset'       => 'اختر أصل',
                'vendor'      => 'مزود (اختياري)',
                'summary'     => 'ملخص الحالة',
                'description' => 'تفاصيل إضافية',
            ],
        ],

        // بنود الصيانة
        'tasks' => [
            'card_title' => 'بنود الصيانة',
            'add_row'    => 'إضافة بند',
            'table' => [
                'index'        => '#',
                'description'  => 'الوصف',
                'performed_at' => 'تاريخ التنفيذ',
                'hours'        => 'الساعات',
                'cost'         => 'التكلفة',
                'technician'   => 'الفني',
                'notes'        => 'ملاحظات',
                'delete'       => 'حذف',
                'total_cost'   => 'إجمالي التكلفة',
            ],
            'placeholders' => [
                'description' => 'وصف البند',
                'technician'  => 'الفني',
                'notes'       => 'ملاحظات',
            ],
            'empty' => 'لا توجد بنود',
        ],

    ],
    'depreciation_title' => 'الاهلاك',


    'back'    => 'رجوع',

    'depreciation' => [
        'index_title'   => 'تشغيلات الإهلاك',
        'create_title'  => 'إنشاء تشغيل إهلاك',
        'show_title'    => 'تفاصيل تشغيل الإهلاك',
        'add_run'       => 'تشغيل جديد',
        'period'        => 'فترة الإهلاك',
        'period_from'   => 'من تاريخ',
        'period_to'     => 'إلى تاريخ',
        'status'        => 'الحالة',
        's_draft'       => 'مسودة',
        's_posted'      => 'مرحّل',
        's_void'        => 'ملغي',
        'all'           => 'الكل',

        'asset'         => 'الأصل',
        'method'        => 'الطريقة',
        'life'          => 'العمر (شهور)',
        'rate'          => 'النسبة',
        'residual'      => 'القيمة المتبقية',
        'cost'          => 'التكلفة',
        'qty'           => 'الكمية',
        'uop_est'       => 'وحدات الإنتاج المقدّرة',
        'uop_units'     => 'وحدات الفترة (UOP)',
        'amount'        => 'قيمة الشهر',
        'accum_before'  => 'مجمع قبل',
        'accum_after'   => 'مجمع بعد',
        'nbv_before'    => 'صافي قبل',
        'nbv_after'     => 'صافي بعد',
        'lines_count'   => 'عدد البنود',
        'total_amount'  => 'إجمالي الإهلاك',
        'preview'       => 'معاينة',
        'save_draft'    => 'حفظ كمسودة',
        'post'          => 'ترحيل',
        'unpost'        => 'إلغاء الترحيل',
        'unpost_confirm' => 'هل تريد إلغاء الترحيل؟',
        'export_csv'    => 'تصدير CSV',
        'print'         => 'طباعة',
        'run_print_title' => 'تشغيل إهلاك — تقرير',

        'notes_hint'    => 'مثال: تشغيل شهر سبتمبر 2025',
        'saved_draft'   => 'تم حفظ التشغيل كمسودة.',
        'posted_ok'     => 'تم ترحيل التشغيل وتحديث الأصول.',
        'unposted_ok'   => 'تم إلغاء ترحيل التشغيل.',
        'already_posted' => 'تم ترحيل هذا التشغيل مسبقًا.',
        'not_posted'    => 'هذا التشغيل ليس مرحّلاً.',
        'uop_used_to_date' => 'UOP: وحدات مستخدمة حتى تاريخه',
        'uop_remaining'    => 'UOP: وحدات متبقية للعمر',
        'status_draft'  => 'مسودة',
        'status_posted' => 'مُرحّل',
        'status_void'   => 'ملغى',
        'last_dep_date'        => 'تاريخ آخر إهلاك',
        'depreciable_base'     => 'الأساس القابل للإهلاك',
        'summary'              => 'ملخص الإهلاك',
        'accumulated'          => 'مجمع الإهلاك',
        'nbv'                  => 'القيمة الدفترية الصافية',
        'progress'             => 'نسبة الإهلاك من الأساس',
        'policy_details'       => 'تفاصيل سياسة الإهلاك',

        // وحدات الإنتاج UOP
        'uop_used_remain'      => 'الوحدات المستخدمة / المتبقية',
        'uop_used_x_of_y'      => 'تم استخدام :used من :total وحدة',
        // تنبيهات الفترة
        'warn_posted_for_period' => 'هناك تشغيل مُرحّل بالفعل لهذه الفترة. راجع التشغيل السابق أو اختر فترة أخرى. لن يُسمح بترحيل مكرر لنفس الفترة.',
        'period_already_posted'  => 'لا يمكن ترحيل هذه الفترة لوجود تشغيل مُرحّل سابقًا بنفس التاريخ. فضلاً اختر فترة مختلفة أو ألغِ ترحيل التشغيل السابق.',
    ],
    'in_service_date' => 'تاريخ الدخول للخدمة',
    'residual_value'  => 'القيمة المتبقية',
    'useful_life_months'         => 'العمر الانتاجي بالشهور',




    'uop_total_estimated_units' => 'إجمالي وحدات الإنتاج المقدّرة للعمر',


    // نص توضيحي تحت الحقول المطلوبة
    'required_field' => 'حقل إلزامي',
    // شروحات (Hints) لحقول الإهلاك تظهر كـ help-text تحت الحقول
    'hints' => [
        'in_service_date' => 'التاريخ الذي أصبح فيه الأصل جاهزًا للاستخدام ويبدأ منه احتساب الإهلاك.',
        'residual_value'  => 'قيمة الأصل المتوقعة عند نهاية عمره الإنتاجي (لا يتم إهلاكها).',
        'dep_method'      => 'اختر طريقة احتساب الإهلاك: SL قسط ثابت شهري، DDB نسبة متناقصة على صافي القيمة، UOP بناءً على وحدات الإنتاج.',
        'useful_life_months' => 'إجمالي مدة الإهلاك بالشهور. مطلوبة في SL/DDB إذا لم تُدخل نسبة.',
        'dep_rate'        => 'نسبة سنوية (٪). في SL تقسّم على 12 شهريًا. في DDB يمكن استخدامها بدل 2×(1/العمر).',
        'uop_total_estimated_units' => 'إجمالي وحدات الإنتاج المتوقعة طوال عمر الأصل (مثل عدد الصفحات للطابعة). وحدات كل شهر تُدخل لاحقًا في شاشة تشغيل الإهلاك.',
    ],
    // رسائل تحقق مساعدة للفورم
    'validation' => [
        'life_or_rate'       => 'في طريقتي SL أو DDB يجب إدخال العمر بالشهور أو نسبة الإهلاك.',
        'uop_total_required' => 'يجب إدخال إجمالي وحدات الإنتاج للعمر عند اختيار طريقة UOP.',
        'required_basic' => 'يرجى إدخال الحقول الأساسية المطلوبة.',

    ],
    'warning' => 'تحذير',

    'help' => array_merge($lang['help'] ?? [], [
        'required_hint'               => 'حقل إلزامي',
        'dep_method'                  => 'اختر طريقة الإهلاك: SL قسط ثابت شهريًا، DDB نسبة متسارعة على صافي الدفتر، UOP بناءً على وحدات الإنتاج.',
        'dep_rate'                    => 'النسبة السنوية للإهلاك (٪). للحساب الشهري تُقسّم تلقائيًا على 12.',
        'useful_life_months'          => 'العمر الإنتاجي المقدر للأصل بوحدة الشهور.',
        'uop_total_estimated_units'   => 'إجمالي وحدات الإنتاج المتوقعة طوال عمر الأصل — تُستخدم مع طريقة UOP.',
        'in_service_date'             => 'التاريخ الذي أصبح فيه الأصل جاهزًا للاستخدام ومنه يبدأ احتساب الإهلاك.',
        'residual_value'              => 'القيمة المتوقعة عند نهاية العمر الإنتاجي (لا يتم إهلاكها).',
    ]),
    'audits_title' => 'جرد الاصول',
    // ==== عامة ====
    'yes'               => 'نعم',
    'no'                => 'لا',
    'updated_at'                => 'تاريخ التحديث',
    'open'                => 'فتح',
    'clear'                => 'مسح',

    'audit' => [
        'index_title'     => 'جرد الأصول',
        'start_new'       => 'بدء جرد جديد',
        'start_now'       => 'ابدأ الآن',
        'panel_title'     => 'لوحة الجرد',
        'scan_placeholder' => 'امسح QR أو أدخل كود الأصل...',
        'scan_btn'        => 'مسح',
        'export'          => 'تصدير CSV',
        'reconcile'       => 'تسوية/إقفال',
        'void'            => 'إلغاء الجرد',
        'void_confirm'    => 'هل أنت متأكد من إلغاء هذا الجرد؟',
        'reconcile_confirm' => 'تطبيق التغييرات المحددة على الأصول وإقفال الجرد؟',
        'open_first'      => 'من فضلك افتح جردًا أولًا.',
        'optional'        => 'اختياري',
        'scope_location'  => 'نطاق الجرد (الموقع)',
        'scope_store'     => 'نطاق الجرد (المخزن)',
        'scope_hint'      => 'اترك الحقول فارغة لجرد كل الأصول النشطة.',
        'all'             => 'الكل',

        // Counters
        'expected'        => 'المتوقع',
        'scanned'         => 'الممسوح',
        'matched'         => 'مطابق',
        'mismatch'        => 'اختلاف',
        'missing'         => 'مفقود',
        'extra'           => 'زيادة',

        // Line columns
        'result'          => 'النتيجة',
        'apply_change'    => 'تطبيق التغيير',
        'scans'           => 'مرات المسح',

        // Results labels
        'result_matched'            => 'مطابق',
        'result_location_mismatch'  => 'اختلاف موقع',
        'result_store_mismatch'     => 'اختلاف مخزن',
        'result_status_mismatch'    => 'اختلاف حالة',
        'result_missing'            => 'مفقود',
        'result_extra'              => 'زيادة',

        // Status labels
        's_draft'       => 'مسودة',
        's_in_progress' => 'قيد التنفيذ',
        's_reconciled'  => 'مُقفل (تمت التسوية)',
        's_void'        => 'ملغي',

        // messages
        'started_ok'        => 'تم بدء الجرد بنجاح.',
        'reconciled_ok'     => 'تمت التسوية وإقفال الجرد.',
        'void_ok'           => 'تم إلغاء الجرد.',
        'cannot_void_closed' => 'لا يمكن إلغاء جرد مُقفل.',
        'not_in_progress'   => 'الجرد ليس قيد التنفيذ.',
        'result_mismatch'           => 'اختلاف',
        // رسائل/توست/تأكيدات
        'scan_done'           => 'تم تسجيل المسح للأصل.',
        'scan_unknown_extra'  => 'لا يوجد أصل بهذا الكود — تم تسجيله كزيادة.',
        'line_updated'        => 'تم تحديث بيانات السطر.',
        'apply_on'            => 'تم تفعيل تطبيق التغيير للسطر.',
        'apply_off'           => 'تم إلغاء تطبيق التغيير للسطر.',
        'filters_applied'     => 'تم تطبيق المرشّحات.',
        'export_ready'        => 'جاري تجهيز ملف التصدير…',
        'unknown_asset_title'   => 'الأصل غير موجود',
        'unknown_asset_body'    => 'لم يتم العثور على أصل بهذا الكود. هل ترغب في إضافته؟',
        'add_asset'             => 'إضافة أصل',
        'quick_add_title'       => 'إضافة أصل — بيانات أساسية',
        'will_be_created_on_reconcile' => 'لن يُنشأ الأصل الآن، سيتم إنشاؤه تلقائيًا عند إجراء التسوية.',
        'draft_saved'           => 'تم حفظ مسودة بيانات الأصل على سطر الجرد.',
        'only_extra'            => 'هذا الإجراء متاح لبنود الزيادة فقط.',
        'save_draft_ok' => 'تم حفظ مسودة الأصل بنجاح.',
        'add_asset_hint' => 'سيتم إنشاء الأصل كمسودة ببيانات أساسية وربطه بالجرد الحالي. يمكنك استكمال البيانات لاحقًا، وستُطبَّق الكمية والموقع والحالة عند التسوية.',

        // أصل غير موجود + حفظ مسودة
        'asset_not_found_title' => 'الأصل غير موجود',
        'asset_not_found_msg'   => 'الكود المدخل غير موجود. هل تريد إضافة أصل جديد بهذا الكود؟',
        'asset_not_found_add'   => 'إضافة أصل',
        'create_asset'          => 'إنشاء أصل',
        'draft_failed'          => 'تعذر حفظ مسودة الأصل.',

    ],

    // (اختياري) نصوص للفلاتر المتقدمة أعلى الجدول
    'filters' => [
        'search_placeholder' => 'ابحث بالاسم أو الكود...',
        'brand'              => 'الماركة',
        'model'              => 'الموديل',
        'status'             => 'الحالة',
        'store'              => 'المخزن',
        'location'           => 'الموقع',
        'apply'              => 'تطبيق',
        'reset'              => 'إعادة ضبط',
    ],

    // (اختياري) رسائل Toast عامة يمكن استخدامها
    'toast' => [
        'saved'        => 'تم الحفظ بنجاح.',
        'updated'      => 'تم التحديث بنجاح.',
        'deleted'      => 'تم الحذف بنجاح.',
        'error'        => 'حدث خطأ غير متوقع.',
        'line_updated' => 'تم تحديث السطر.',
        'scan_ok'      => 'تم تسجيل المسح.',
    ],
    // عامة للواجهات والفلاتر
    'all'            => 'الكل',
    'apply_filters'  => 'تطبيق الفلاتر',
    'reset'          => 'إعادة الضبط',
    'page'               => 'الصفحة',
    'total'              => 'الإجمالي',
    'error_happened'     => 'حدث خطأ غير متوقع',
    'are_you_sure'       => 'هل أنت متأكد؟',
    'ok'                 => 'موافق',

    //disposal
    'disposal_and_retired'                 => 'التخلص / الشطب',
    'disposal' => [

        'qty_not_available'       => 'الكمية غير متاحة',
        'index_title' => 'طلبات التخلص/الشطب',
        'create_title' => 'إنشاء طلب تخلّص',
        'show_title'  => 'تفاصيل طلب التخلّص',
        'execute_title' => 'تنفيذ التخلّص',
        'add_request' => 'طلب تخلّص جديد',
        'request_no'  => 'رقم الطلب',
        'method'      => 'طريقة التخلّص',
        'm_sale'      => 'بيع',
        'm_donation'  => 'تبرع',
        'm_scrap'     => 'شطب',
        'assets_select' => 'اختر الأصول',
        'reason'      => 'السبب',
        'notes'       => 'ملاحظات',
        'attachments' => 'مرفقات',
        'items'       => 'البنود',
        'requested_at' => 'تاريخ الطلب',
        'submit'      => 'إرسال للاعتماد',
        'execute'     => 'تنفيذ',
        'req'         => 'الطلب',
        'committee'   => 'اللجنة',
        'exec'        => 'التنفيذ',
        'level'       => 'المستوى',
        'decision_pending' => 'قيد الاعتماد',
        'decision_approved' => 'معتمد',
        'decision_rejected' => 'مرفوض',
        'decision_returned' => 'معاد للتعديل',
        'status_draft'     => 'مسودة',
        'status_submitted' => 'مُرسل',
        'status_under_review' => 'قيد المراجعة',
        'status_approved'  => 'مُعتمد',
        'status_rejected'  => 'مرفوض',
        'status_cancelled' => 'ملغي',
        'status_executed'  => 'تم التنفيذ',
        'status_closed'    => 'مغلق',
        'only_draft_can_submit' => 'فقط المسودات يمكن إرسالها.',
        'only_approved_can_execute' => 'فقط الطلبات المعتمدة يمكن تنفيذها.',
        'audit_active_block' => 'يوجد جرد نشط يمنع الإجراء.',
        'asset_already_retired' => 'الأصل خارج الخدمة بالفعل.',
        'approval_wrong_status' => 'لا يمكن تسجيل قرار في هذه الحالة.',
        'cannot_cancel_now' => 'لا يمكن الإلغاء في هذه الحالة.',
        'asset_has_open_assignment' => 'لا يمكن التنفيذ: يوجد إسناد مفتوح لهذا الأصل. يرجى إرجاع الأصل أولاً.',
        'status'    => 'الحالة',
        'approvals'    => 'الموافقات',
        'submitted' => 'تم إرسال طلب التخلص/الشطب للاعتماد.',
        // طرق التخلّص
        'method_sale'     => 'بيع',
        'method_donation' => 'تبرّع',
        'method_scrap'    => 'شطب/إعدام',
        'qty_exceeds_available' => 'الكمية المطلوبة تتجاوز المتاح أو أن المتاح صفر.',
        'submitted_and_auto_approved' => 'تم الإرسال والاعتماد تلقائياً لعدم تعيين معتمدين.',
        'executed' => 'تم التنفيذ',
        'not_assigned_to_you'       => 'غير مخوّل لاعتماد هذا السجل.',
        // حالات قرارات الاعتماد
        'a_pending'  => 'معلّق',
        'a_approved' => 'معتمد',
        'a_rejected' => 'مرفوض',
        'a_returned' => 'معاد للتعديل',
    ],
    'attachments'    => 'المرفقات',
    'add'    => 'اضافه',
    'approve'  => 'اعتماد',
    'reject'   => 'رفض',
    'returned' => 'إرجاع للتعديل',
    'qty_requested' => 'الكمية المطلوبة',
    'qty_execute'   => 'الكمية المنفذة',

    //convert_to_assets

    'convert_to_assets_title'    => 'تحويل اصناف الي اصول',

        'convert_to_assets' => [
            'skip_not_selected' => 'السطر غير محدد.',
        'skip_unit_missing' => 'تعذر تحديد وحدة الحركة.',
        'col_select'        => 'اختيار',
        'index_title' => 'تحويل أصناف المشتريات إلى أصول',
        'show_title'  => 'تفاصيل محضر التحويل',
        'search_placeholder' => 'ابحث برقم المحضر/المورد',
        'all_suppliers' => 'كل الموردين',
        'all_stores'    => 'كل المخازن',

        'col_sr'      => 'م',
        'col_code'    => 'رقم المحضر',
        'col_date'    => 'التاريخ',
        'col_supplier'=> 'المورد',
        'col_store'   => 'المخزن',
        'col_status'  => 'الحالة',
        'col_actions' => 'الإجراءات',

        'status_approved' => 'مُعتمد',
        'open_for_conversion' => 'فتح للتحويل',

        'items_table_title' => 'الأصناف + الأرصدة المتاحة بالدفعات',
        'qty_base_hint'     => 'الكمية تُكتب بالوحدة الأساسية (أصغر وحدة)',
        'col_product'       => 'الصنف',
        'col_accepted_unit' => 'الوحدة المقبولة',
        'col_batch'         => 'الدفعة',
        'col_expiry'        => 'الصلاحية',
        'col_balance_base'  => 'الرصيد (أساس)',
        'col_qty_to_convert_base' => 'الكمية للتحويل (أساس)',
        'ph_enter_qty_le_balance' => 'أدخل كمية ≤ الرصيد',

        'no_balances' => 'لا يوجد رصيد متاح لهذا الصنف في هذا المخزن',
        'no_items'    => 'لا توجد أصناف مقبولة في هذا المحضر',

        'skipped_rows_title' => 'تم تجاوز بعض الأسطر:',
        'skipped_reason'     => 'سطر #:i - :r',

        'err_no_lines'          => 'لا توجد أسطر محددة للتحويل.',
        'err_nothing_converted' => 'لم يتم تحويل أي سطر: تحقق من الرصيد/البيانات.',
        'skip_incomplete'       => 'بيانات غير مكتملة.',
        'skip_balance'          => 'رصيد غير متاح.',
        'skip_insufficient'     => 'رصيد غير كافٍ.',
        'skip_mismatch'         => 'بند محضر غير مطابق.',
        'skip_product_missing'  => 'الصنف غير موجود.',
        'skip_code_missing'     => 'product_code مفقود لهذا الصنف.',

        'success_msg' => 'تم تحويل :n بند/بنود إلى أصول.',
        'btn_convert' => 'تحويل المحدد إلى أصول',
        'btn_back'    => 'رجوع',

        'mv_note_prefix' => 'تحويل إلى أصل من محضر',
    ],
 'asset_conversions' => [
        'index_title'   => 'تحويلات الأصول',
        'show_title'    => 'عرض تحويل أصل',
        'search_placeholder' => 'ابحث بالدفعة/الصنف/المخزن',
        'col_sr'        => 'م',
        'col_product'   => 'الصنف',
        'col_store'     => 'المخزن',
        'col_batch'     => 'الدفعة',
        'col_expiry'    => 'الصلاحية',
        'col_qty'       => 'الكمية (أساس)',
        'col_status'    => 'الحالة',
        'col_actions'   => 'الإجراءات',
        'status_active'   => 'ساري',
        'status_cancelled'=> 'ملغي',
        'btn_show'      => 'عرض',
        'btn_cancel'    => 'إلغاء التحويل',
        'btn_back'      => 'رجوع',
        'btn_close'     => 'إغلاق',
        'btn_confirm_cancel' => 'تأكيد الإلغاء',
        'modal_cancel_title'  => 'تأكيد إلغاء التحويل',
        'modal_cancel_reason' => 'سبب الإلغاء (اختياري)',
        'modal_cancel_ph'     => 'اكتب سبب الإلغاء إن رغبت',
        'modal_cancel_warn'   => 'سيتم إعادة الكمية إلى رصيد المخزن وتخفيض كمية الأصل وتسجيل حركة ضبط موجبة.',
        'msg_cancelled'       => 'تم إلغاء التحويل وإرجاع الكميات بنجاح.',
        'mv_cancel_note'      => 'Cancel asset conversion',
        'err_not_implemented' => 'ميزة التعديل غير مفعلة حالياً.',
    ],
  // Titles
    'master_movement_title'    => 'تقرير سجل الأصول الموحّد',
    'master_movement_subtitle' => 'دمج السجل العام مع الإضافات (تحويل/مباشر) وتحركات/تكليفات الأصل في عرض واحد.',

    // General UI
    'filters_title'       => 'الفلاتر',
    'reset_filters'       => 'إعادة ضبط',
    'export_excel'        => 'تصدير Excel',
    'select_placeholder'  => 'اختر…',
    'no_data_until_filter'=> 'لا توجد بيانات حتى تقوم بتطبيق الفلاتر.',
    'no_results'          => 'لا توجد نتائج',
    'generated_at'        => 'تاريخ إنشاء التقرير',
    'items_count'         => 'عدد السجلات',
    'landscape_mode'      => 'وضع العرض أفقي',
    'note_report_generated_now' => 'القيم محسوبة حتى لحظة إنشاء التقرير.',

    // Filters
    'filter_purchase_from'=> 'تاريخ الشراء من',
    'filter_purchase_to'  => 'تاريخ الشراء إلى',
    'addition_source'     => 'مصدر الإضافة',
    'source_conversion'   => 'تحويل من مخزون',
    'source_direct'       => 'إضافة مباشرة',
    'any'                 => 'الكل',
    'cost_range'          => 'نطاق التكلفة',
    'nbv_range'           => 'نطاق NBV',
    'min'                 => 'من',
    'max'                 => 'إلى',

    // Table columns
    'col_code'            => 'كود',
    'col_name'            => 'اسم الأصل',
    'col_category'        => 'الفئة',
    'col_brand'           => 'الماركة',
    'col_model'           => 'الموديل',
    'col_serial'          => 'الرقم التسلسلي',
    'col_location'        => 'الموقع',
    'col_custodian'       => 'المسؤول',
    'col_purchase_date'   => 'تاريخ الشراء',
    'col_cost'            => 'التكلفة',
    'col_status'          => 'الحالة',
    'col_addition_source' => 'مصدر الإضافة',
    'col_last_movement'   => 'آخر حركة',
    'col_nbv'             => 'القيمة الدفترية (NBV)',


    // Filters
    'qty_range'           => 'نطاق الكمية',


    // Table columns
    'col_store'           => 'المخزن',
    'col_supplier'        => 'المورد',
    'col_qty_total'       => 'الكمية الإجمالية',
    'col_qty_assigned'    => 'الكمية المخصّصة',
    'col_qty_available'   => 'الكمية المتاحة',
    'reports_title'           => 'تقارير الأصول',
    'master_movement_report_title'           => 'تقرير سجل الاصول',

        'depreciation_report_title' => 'تقرير الإهلاك والقيم الدفترية',
        'tab_lines'   => 'سطور الإهلاك',
        'tab_runs'    => 'ملخص التشغيلات',
        'tab_missing' => 'أصول غير مُستهلكة',

        'filter_period_from' => 'الفترة من',
        'filter_period_to'   => 'الفترة إلى',
        'filter_methods'     => 'طريقة الإهلاك',
        'filter_assets'      => 'الأصول',



        // سطور الإهلاك
        'col_dep_method'   => 'طريقة الإهلاك',
        'col_useful_life'  => 'العمر الإنتاجي (شهر)',
        'col_dep_rate'     => 'معدل الإهلاك',
        'col_period'       => 'الفترة',
        'col_amount'       => 'مبلغ الإهلاك',
        'col_nbv_after'    => 'القيمة الدفترية بعد الإهلاك',

        // ملخص التشغيلات
        'col_lines_count'  => 'عدد السطور',
        'col_total_amount' => 'إجمالي المبالغ',
        'col_posted_at'    => 'تاريخ الترحيل',

        // المفقود
        'col_in_service'   => 'تاريخ بدء الخدمة',
        'col_last_dep'     => 'آخر تاريخ إهلاك',

        // طرق الإهلاك
        'method_straight_line'      => 'القسط الثابت',
        'method_declining_balance'  => 'الرصيد المتناقص',
        'method_uop'                => 'وحدات الإنتاج',


    // حالة تشغيلات الإهلاك (يستخدمها accessors في الموديل)

        'status_draft'  => 'مسودة',
        'status_posted' => 'مُرحّل',
  /* ===================== تقارير الصيانة 360 ===================== */
    'maintenance_report_title' => 'تقرير الصيانة 360',

    // تبويبات
    'tab_orders' => 'أوامر الصيانة',
    'tab_tasks'  => 'مهام الصيانة',

    'org_name' => 'مستشفي دنيا العيون',


    // فلاتر الصيانة
    'filter_date_on'     => 'تطبيق التاريخ على',
    'date_on_reported'   => 'تاريخ البلاغ',
    'date_on_started'    => 'تاريخ البدء',
    'date_on_finished'   => 'تاريخ الإغلاق',
    'date_on_due'        => 'تاريخ الاستحقاق القادم',
    'filter_date_from'   => 'التاريخ من',
    'filter_date_to'     => 'التاريخ إلى',
    'filter_types'       => 'نوع الصيانة',
    'filter_priorities'  => 'الأولوية',
    'filter_technicians' => 'الفني/الفريق',
    'filter_vendors'     => 'المورّدون',


    // أعمدة تبويب الأوامر
    'col_asset_code'   => 'كود الأصل',
    'col_asset_name'   => 'اسم الأصل',
    'col_maint_type'   => 'نوع الصيانة',
    'col_wo_code'      => 'رقم أمر العمل',
    'col_reported_at'  => 'تاريخ البلاغ',
    'col_started_at'   => 'تاريخ البدء',
    'col_finished_at'  => 'تاريخ الإغلاق',
    'col_priority'     => 'الأولوية',
    'col_duration_h'   => 'المدة (ساعات)',
    'col_mttr_h'       => 'MTTR (ساعات)',
    'col_last_pm'      => 'آخر صيانة وقائية',
    'col_overdue'      => 'متأخر؟',
    'col_technicians'  => 'الفنيون',
    'col_total_cost'   => 'التكلفة الإجمالية',

    // أعمدة تبويب المهام
    'col_performed_at' => 'تاريخ التنفيذ',
    'col_hours'        => 'الساعات',
    'col_task_desc'    => 'وصف المهمة',
    'col_technician'        => 'الفني',


    // أنواع الصيانة وحالاتها
    'maintenance_type_preventive' => 'وقائي (PM)',
    'maintenance_type_corrective' => 'تصحيحي (CM)',

    'maintenance_status_open'        => 'مفتوح',
    'maintenance_status_in_progress' => 'قيد التنفيذ',
    'maintenance_status_closed'      => 'مغلق',

    // مستويات الأولوية
    'priority_low'    => 'منخفضة',
    'priority_medium' => 'متوسطة',
    'priority_high'   => 'مرتفعة',
    'priority_urgent' => 'عاجلة',

    // KPIs
    'kpi_pm_compliance'  => 'نسبة الالتزام بالوقائي PM%',
    'kpi_mttr_hours'     => 'متوسط زمن الإصلاح MTTR (ساعات)',
    'kpi_downtime_hours' => 'إجمالي ساعات التوقف',
    'kpi_top_assets_cost'=> 'أعلى أصل تكلفة',
    'audit_report_title' => 'تقرير الجرد والفروقات',

    // تبويبات
    'tab_lines_audits'      => 'سطور الجرد',
    'tab_audits'     => 'ملخص الجرد',
    'tab_unreviewed' => 'أصول خارج آخر جرد',

    // فلاتر
    'filter_audit_codes'   => 'أرقام الجرد',
    'filter_results'       => 'حالة المطابقة',
    'audit_date_on_started'=> 'تاريخ بدء الجرد',
    'audit_date_on_finished'=> 'تاريخ انتهاء الجرد',

    // أعمدة عامة
    'col_audit_code'       => 'رقم الجرد',
    'col_title'            => 'العنوان',
    'col_expected_count'   => 'المتوقع',
    'col_scanned_count'    => 'الممسوح',
    'col_matched_count'    => 'المطابق',
    'col_mismatch_count'   => 'اختلاف',
    'col_missing_count'    => 'مفقود',
    'col_extra_count'      => 'زايد',
    'col_compliance'       => 'نسبة المطابقة',

    'col_exp_location'     => 'الموقع المتوقع',
    'col_found_location'   => 'الموقع الفعلي',
    'col_result'           => 'نتيجة الجرد',
    'col_scan_count'       => 'عدد المسح',
    'col_last_scanned_at'  => 'آخر مسح',
    'col_user'             => 'المستخدم',
    'col_nbv_at_audit'     => 'القيمة الدفترية وقت الجرد',
    'col_notes'            => 'ملاحظات',

    // نتائج الجرد
    'audit_r_matched'           => 'مطابق',
    'audit_r_missing'           => 'مفقود',
    'audit_r_extra'             => 'زايد',
    'audit_r_location_mismatch' => 'اختلاف موقع',
    'audit_r_store_mismatch'    => 'اختلاف مخزن',
    'audit_r_status_mismatch'   => 'اختلاف حالة',

    // حالات الجرد
    'audit_s_draft'     => 'مسودة',
    'audit_s_running'   => 'قيد التنفيذ',
    'audit_s_finished'  => 'مُنتهٍ',
    'audit_s_cancelled' => 'ملغى',

    // KPIs
    'kpi_audit_compliance' => 'نسبة المطابقة',
    'kpi_missing_count'    => 'عدد الأصول المفقودة',
    'kpi_missing_value'    => 'القيمة الدفترية المفقودة',
    'kpi_unreviewed'       => 'أصول خارج آخر جرد',
    'documents_report_title' => 'تقرير الوثائق والامتثال',

    // تبويبات
    'documents_tab_lines'    => 'سطور الوثائق',
    'tab_by_type'  => 'ملخص حسب النوع',
    'tab_by_asset' => 'ملخص حسب الأصل',

    // فلاتر
    'filter_doc_types' => 'نوع الوثيقة',
    'filter_suppliers' => 'الجهة المُصدِرة',
    'filter_has_file'  => 'حالة المرفقات',

    // أعمدة (تفصيلي)
    'col_doc_type'    => 'نوع الوثيقة',
    'col_doc_no'      => 'رقم الوثيقة',
    'col_doc_date'    => 'تاريخ الإصدار',
    'col_issuer'      => 'الجهة المُصدِرة',
    'col_has_file'    => 'مرفق',
    'col_days_since'  => 'أيام منذ الإصدار',

    // أعمدة (ملخصات)
    'col_docs_count'       => 'عدد الوثائق',
    'col_with_file'        => 'بوثائق مرفقة',
    'col_without_file'     => 'بدون مرفقات',
    'col_avg_days_since'   => 'متوسط الأيام منذ الإصدار',
    'col_latest_doc_date'  => 'أحدث تاريخ إصدار',

    // حالات المرفقات
    'doc_has_file' => 'مرفق',
    'doc_no_file'  => 'بدون مرفق',

    // KPIs
    'kpi_docs_total'        => 'إجمالي الوثائق',
    'kpi_with_attachment'   => 'بوثائق مرفقة',
    'kpi_without_attachment'=> 'بدون مرفقات',
    'kpi_total_cost'        => 'إجمالي تكلفة الوثائق',

    // --- Disposal Lifecycle Report ---
'disposal_lifecycle_report_title' => 'تقرير دورة التخلص/الشطب',

// Tabs
'tab_requests'   => 'الطلبات',
'tab_executions' => 'التنفيذات',
'tab_approvals'  => 'الموافقات',
'tab_by_reason'  => 'حسب السبب',

// Columns
'col_request_no'   => 'رقم الطلب',
'col_requested_at' => 'تاريخ الطلب',
'col_method'       => 'نوع التخلص',
'col_reason'       => 'السبب',
'col_created_by'   => 'منشئ الطلب',
'col_assets_count' => 'عدد الأصول',
'col_qty'          => 'الكمية',
'col_nbv_total'    => 'إجمالي القيمة الدفترية (NBV)',
'col_proceeds'     => 'المتحصلات',
'col_profit'       => 'الربح/الخسارة',
'col_executed_at'  => 'تاريخ التنفيذ',
'col_level'        => 'المستوى',
'col_role'         => 'الدور/اللجنة',
'col_assignee'     => 'المُكلَّف',
'col_approver'     => 'المُعتمِد',
'col_decision'     => 'القرار',
'col_decided_at'   => 'تاريخ القرار',
'col_ops_count'    => 'عدد العمليات',

// Status labels
'disposal_status_pending'  => 'قيد الانتظار',
'disposal_status_approved' => 'موافق عليه',
'disposal_status_rejected' => 'مرفوض',
'disposal_status_executed' => 'مُنفَّذ',
'disposal_status_closed'   => 'مغلق',
'disposal_status_cancelled'   => 'ملغي',


// Methods
'disposal_method_sale'     => 'بيع',
'disposal_method_scrap'    => 'خردة',
'disposal_method_donation' => 'تبرع',

// Approval decisions
'approval_pending'  => 'قيد الاعتماد',
'approval_approved' => 'معتمد',
'approval_rejected' => 'مرفوض',

'filter_status' => 'الحالة',
'filter_method' => 'نوع التخلص',
'filter_reason' => 'السبب',

];
