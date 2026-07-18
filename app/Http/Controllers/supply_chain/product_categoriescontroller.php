<?php
namespace App\Http\Controllers\supply_chain;

use Illuminate\Http\Request;
use App\models\general\branch;
use App\Traits\web\file_storage;
use App\Http\Controllers\Controller;
use App\models\supply_chain\productcategory;
use Yajra\DataTables\Facades\DataTables;

class product_categoriescontroller extends Controller
{
    use file_storage;


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = ProductCategory::with('branchRelation')->get();

            return DataTables::of($categories)
            ->addColumn('branch_name', function ($row) {
                $locale = app()->getLocale();

                if ($row->branch) {
                    // الحصول على القيمة الأصلية للفرع
                    $branchField = trim($row->branch);

                    // محاولة فك تشفير الـ JSON
                    $branchIds = json_decode($branchField, true);

                    // إذا لم تفك الشفرة بنجاح أو لم تحصل على مصفوفة، نتحقق مما إذا كانت قيمة رقمية مفردة
                    if (is_null($branchIds) || !is_array($branchIds)) {
                        // في حال كانت قيمة رقمية أو نصية تمثل رقم فرع مفرد، نحولها إلى مصفوفة
                        $branchIds = [$branchField];
                    }

                    // في حال تم الحصول على مصفوفة من الـ IDs
                    if (count($branchIds) > 0) {
                        // جلب بيانات الفروع التي تحقق الشرط
                        $branches = branch::whereIn('id', $branchIds)->get();

                        // استخراج أسماء الفروع مع الترجمة حسب اللغة الحالية
                        $branchNames = $branches->map(function ($branch) use ($locale) {
                            // إذا كان الحقل name مخزنًا كـ JSON مع ترجمات
                            $nameRaw = trim($branch->name);
                            $decoded = json_decode($nameRaw, true);
                            return is_array($decoded) ? ($decoded[$locale] ?? '-') : $nameRaw;
                        })->toArray();

                        return implode(' / ', $branchNames);
                    }
                }

                return '-';
            })


                ->addColumn('action', function ($row) {
                    $editUrl = route('product_categories.edit', $row->id);
                    $deleteBtn = "<button class='btn btn-danger btn-sm delete-btn' data-id='{$row->id}'><i class='ri-delete-bin-line'></i></button>";

                    return "<a href='{$editUrl}' class='btn btn-warning btn-sm'><i class='ri-edit-line'></i></a> $deleteBtn";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('supply_chain.settings.product_categories.index');
    }


    public function create()
{
    // جلب المجموعات الرئيسية من جدول الفئات (حيث الحقل type يساوي 'main')
    $mainCategories =  productcategory::where('type', 'main')->get();

    // جلب جميع الفروع من جدول الفروع
    $branches = branch::where('status', 'active')->get();
    return view('supply_chain.settings.product_categories.create', compact('mainCategories', 'branches'));
}

public function store(Request $request)
{
    // تعريف قواعد التحقق من صحة البيانات
    $rules = [
        'name_ar'      => 'required|string|max:255',
        'name_en'      => 'required|string|max:255',
        'status'       => 'required|in:0,1',
        'group_type'   => 'required|in:main,sub',
        // عند اختيار النوع الرئيسي، يجب تحديد الفرع كـ array ويوجد على الأقل عنصر واحد
        'branch'       => 'required|array|min:1',
        'branch.*'     => 'required|numeric|exists:branches,id',
        // ترتيب اختياري يقبل أرقام فقط
        'sort'         => 'nullable|numeric',
    ];

    // في حال اختيار المجموعة الفرعية، يجب تحديد المجموعة الرئيسية (من نفس الجدول)
    if ($request->group_type === 'sub') {
        $rules['main_group_id'] = 'required|exists:product_categories,id';
    }

    // تنفيذ التحقق من صحة البيانات
    $validatedData = $request->validate($rules);

    // إنشاء سجل جديد للفئة
    $productCategory = new ProductCategory();
    $productCategory->name_ar = $validatedData['name_ar'];
    $productCategory->name_en = $validatedData['name_en'];
    $productCategory->status  = $validatedData['status'];
    $productCategory->type    = $validatedData['group_type'];
    // تخزين الفروع المختارة كـ JSON
    $productCategory->branch  = json_encode($request->branch);
    $productCategory->sort    = $validatedData['sort'] ?? null;

    // في حالة اختيار المجموعة الفرعية، يتم تحديد الفئة الرئيسية من الحقل main_group_id
    if ($request->group_type === 'sub') {
        $productCategory->category = $validatedData['main_group_id'];
    }

    $productCategory->save();

    // إعادة التوجيه مع رسالة نجاح إلى صفحة index
    return redirect()->route('product_categories.index')
                     ->with('success', trans('supply_chain_trans.category_created_successfully'));
}

public function edit($id)
{
    // البحث عن الفئة المطلوب تعديلها
    $category = ProductCategory::findOrFail($id);

    // التحقق مما إذا كانت هذه فئة رئيسية ولديها فئات فرعية
    $hasSubCategories = false;
    if ($category->type == 'main') {
        $hasSubCategories = ProductCategory::where('category', $id)->exists();
    }

    // جلب المجموعات الرئيسية من جدول الفئات (حيث الحقل type يساوي 'main')
    $mainCategories = ProductCategory::where('type', 'main')->get();

    // جلب جميع الفروع من جدول الفروع
    $branches = branch::where('status', 'active')->get();
    return view('supply_chain.settings.product_categories.edit', compact('category', 'mainCategories', 'branches', 'hasSubCategories'));
}

public function update(Request $request, $id)
{
    // البحث عن الفئة المطلوب تحديثها
    $category = ProductCategory::findOrFail($id);

    // تعريف قواعد التحقق من صحة البيانات
    $rules = [
        'name_ar'    => 'required|string|max:255',
        'name_en'    => 'required|string|max:255',
        'status'     => 'required|in:0,1',
        'group_type' => 'required|in:main,sub',
        'branch'     => 'required|array|min:1', // تعديل: التحقق من مصفوفة فروع
        'branch.*'   => 'exists:branches,id',   // تأكيد وجود كل فرع
        'sort'       => 'nullable|numeric',
    ];

    // في حال اختيار المجموعة الفرعية، يجب تحديد المجموعة الرئيسية
    if ($request->group_type === 'sub') {
        $rules['main_group_id'] = 'required|exists:product_categories,id';
    }

    // تنفيذ التحقق من صحة البيانات
    $validatedData = $request->validate($rules);

    // التحقق إذا كان السجل الحالي فئة رئيسية ويحتوي على فئات فرعية
    if ($category->type === 'main') {
        $hasSubCategories = ProductCategory::where('category', $id)->exists();
        if ($hasSubCategories && $request->group_type === 'sub') {
            return redirect()->back()
                ->withInput()
                ->with('error', trans('supply_chain_trans.cannot_change_type_has_subcategories'));
        }
    }

    // تحديث بيانات الفئة
    $category->name_ar = $validatedData['name_ar'];
    $category->name_en = $validatedData['name_en'];
    $category->status  = $validatedData['status'];
    $category->type    = $validatedData['group_type'];

    // تخزين الفروع كمصفوفة JSON
    $category->branch = json_encode($validatedData['branch']);

    $category->sort = $validatedData['sort'] ?? null;

    if ($request->group_type === 'sub') {
        $category->category = $validatedData['main_group_id'];
    } else {
        $category->category = null;
    }

    $category->save();

    return redirect()->route('product_categories.index')
                     ->with('success', trans('supply_chain_trans.category_updated_successfully'));
}

public function destroy($id)
{
    try {
        // البحث عن الفئة المراد حذفها
        $category = ProductCategory::findOrFail($id);

        // التحقق مما إذا كانت هذه فئة رئيسية ولديها فئات فرعية
        if ($category->type == 'main') {
            $hasSubCategories = ProductCategory::where('category', $id)->exists();
            if ($hasSubCategories) {
                return response()->json([
                    'success' => false,
                    'message' => trans('supply_chain_trans.cannot_delete_has_subcategories')
                ]);
            }
        }

        // تنفيذ عملية الحذف (استخدام SoftDeletes)
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => trans('supply_chain_trans.category_deleted_successfully')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => trans('supply_chain_trans.error_occurred') . ': ' . $e->getMessage()
        ]);
    }
}

}
