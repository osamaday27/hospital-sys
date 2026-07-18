<?php

namespace App\Http\Controllers\finance\pricing_management;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\finance\pricelistservice;
use App\models\general\currencies;
use App\models\general\branch;
use App\models\finance\service;
use App\models\finance\servicesublevel;
use App\models\finance\price_lists;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class pricing_of_servicescontroller extends Controller
{
    public function index()
    {
        return view('finance.pricing_contracts_management.pricing_management.price_lists.price_list_set');
    }

    public function create($id)
    {
       
        $price_list_id = Crypt::decryptString($id);
        return view('finance.pricing_contracts_management.pricing_management.price_lists.price_list_set', compact('price_list_id'));
    }
    public function update(Request $request)
    {
        // التحقق من وجود المعاملات المطلوبة
        $validated = $request->validate([
            'field' => 'required|in:hospital_price,special_price',
            'value' => 'required|numeric',
            'service_id' => 'required|exists:fin_services,id',
        ]);

        try {
            // جلب قيمة المعامل 'pricing_of_services' من الـ URL
            $pricingOfServices = $request->price_list_id;

            // يمكنك الآن فك تشفير القيمة
            $decryptedPriceListId = Crypt::decryptString($pricingOfServices);

            // البحث عن السجل باستخدام المعرف الذي تم تمريره
            $priceRecord = pricelistservice::where('price_list_id', $decryptedPriceListId)
                ->where('service_id', $request->service_id)
                ->first();

            // إذا لم يكن السجل موجودًا، قم بإنشاء سجل جديد
            if (!$priceRecord) {
                $priceRecord = new pricelistservice();
                $priceRecord->price_list_id = $decryptedPriceListId; // تعيين price_list_id المفكوك
                $priceRecord->service_id = $request->service_id;
                $priceRecord->status = $request->status ?? 'active'; // تعيين الحالة (مفعلة أو غير مفعلة)
                $priceRecord->user_id = auth()->id(); // تعيين المستخدم الذي قام بالتحديث
                // تحديث السعر حسب الحقل المحدد
                if ($request->field === 'hospital_price') {
                    // التأكد من أن القيمة ليست صفرًا إذا كانت لا تزال موجودة
                    $priceRecord->hospital_price = $request->value ?? 0.0;
                } elseif ($request->field === 'special_price') {
                    $priceRecord->special_price = $request->value ?? 0.0;
                }
            } else {
                // تحديث السعر حسب الحقل المحدد
                if ($request->field === 'hospital_price') {
                    // التأكد من أن القيمة ليست صفرًا إذا كانت لا تزال موجودة
                    $priceRecord->hospital_price = $request->value ?? 0;
                } elseif ($request->field === 'special_price') {
                    $priceRecord->special_price = $request->value ?? 0;
                }
            }

            // حفظ التغييرات
            $priceRecord->save();

            // إعادة استجابة النجاح
            return response()->json([
                'status' => 'success',
                'message' => 'Price updated successfully.',
            ]);
        } catch (\Exception $e) {
            // معالجة الخطأ
            return response()->json([
                'status' => 'error',
                'message' => 'There was an error updating the price.',
            ]);
        }
    }

    public function getpricing_of_services(Request $request)
    {
        try {
            // فك تشفير price_list_id والتحقق من وجوده
            $priceListId = Crypt::decryptString($request->input('price_list_id'));
            if (!$priceListId) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => trans('pricing_list.price_list_id_required'),
                    ],
                    400,
                );
            }

            // استرجاع الخدمات مع أسعارها بناءً على قائمة الأسعار المختارة
            $services = service::with([
                'pricelistServices' => function ($query) use ($priceListId) {
                    $query->where('price_list_id', $priceListId);
                },
            ])->limit(200)->get();
            

            // تجهيز بيانات الجدول باستخدام DataTables
            return DataTables::of($services)
                ->editColumn('name', fn($service) => $service->getTranslation('name', app()->getLocale()))
                ->editColumn('description', fn($service) => $service->getTranslation('description', app()->getLocale()))
                ->editColumn('level_type', fn($service) => $this->getLevelType($service))
                ->editColumn('hospital_price', fn($service) => $this->generatePriceField($service, 'hospital_price'))
                ->editColumn('special_price', fn($service) => $this->generatePriceField($service, 'special_price'))
                ->editColumn('created_at', fn($service) => Carbon::parse($service->created_at)->format('d/m/Y h:i A'))
                ->rawColumns(['hospital_price', 'special_price'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * استرجاع النوع (Level Type) للخدمة
     */
    private function getLevelType($service)
    {
        $sublevel = $service->sublevel;
        if ($sublevel && $sublevel->level1) {
            $level1Name = $sublevel->level1->getTranslation('name', app()->getLocale());
            $sublevelName = $sublevel->getTranslation('name', app()->getLocale());
            return "{$level1Name} - {$sublevelName}";
        }
        return trans('pricing_list.select_level_type');
    }

    /**
     * إنشاء حقل السعر في الجدول
     */
    private function generatePriceField($service, $field)
    {
        $price = $service->pricelistServices->first();
        $value = $price ? $price->$field : '';
        $placeholder = trans('pricing_list.enter_price');
        return "<input type='text' class='form-control price-input' data-service-id='{$service->id}' data-field='{$field}' value='{$value}' placeholder='{$placeholder}'>";
    }
}