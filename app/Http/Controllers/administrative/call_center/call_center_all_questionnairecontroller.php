<?php

namespace App\Http\Controllers\administrative\call_center;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\Traits\web\get_contract;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\models\administrative\adm_s_question;
use App\Models\Administrative\patient_visits;
use App\models\administrative\adm_b_questionnaire;
use App\Models\Administrative\adm_s_answer_values;
use App\models\administrative\adm_s_questionnaires;
use Illuminate\Contracts\Encryption\DecryptException;
use App\models\administrative\adm_b_questionnaires_details;
use App\models\administrative\reservations;





class call_center_all_questionnairecontroller extends Controller
{
    use file_storage, get_contract;

    public function index()
    {
        return view('administrative.call_center.program.questionnaires.index');
    }


    private function AllQuestionnairesActionButtons($questionnaire)
    {
        // تشفير معرف الاستبيان
        $encryptedQuestionnaireId = Crypt::encryptString($questionnaire->id);

        $actions = '<div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mdi mdi-cog-outline"></i> ' . trans('inpatient_trans.action') . '
        </button>
        <div class="dropdown-menu dropdown-menu-end">';
        // **إظهار زر التعديل فقط إذا لم يكن مغلقًا**
        if ($questionnaire->status !== 'closed') {
            $actions .= '<a class="dropdown-item" href="' . route('all_questionnaires.edit', ['all_questionnaire' => $encryptedQuestionnaireId]) . '">
        <i class="ri-pencil-fill text-warning"></i> ' . trans('general_trans.edit') . '
        </a>';
        }

        // مراجعة الاستبيان
        $actions .= '<a class="dropdown-item" href="' . route('all_questionnaires.show', ['all_questionnaire' => $encryptedQuestionnaireId]) . '">
        <i class="ri-eye-fill text-info"></i> ' . trans('call_center_trans.review_questionnaire') . '
        </a>';
        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteFunction(\'' . $encryptedQuestionnaireId . '\', \'' . $questionnaire->id . '\')">
        <i class="ri-delete-bin-6-line text-danger"></i> ' . trans('general_trans.delete') . '
        </a>';
        // **زر رفع المرفقات**
        $actions .= '<a class="dropdown-item" href="' . route('uploadAttachments', ['id' => $encryptedQuestionnaireId]) . '">
        <i class="ri-attachment-line text-primary"></i> ' . trans('call_center_trans.upload_attachments') . '
        </a>';
        $actions .= '</div></div>';

        return $actions;
    }


    public function getquestionnaires(Request $request)
    {

        // جلب البيانات مع العلاقات المطلوبة: المريض، الاستبيان، المستخدم الذي أنشأ والسيد الذي أغلق الاستبيان
        $questionnaires = adm_b_questionnaire::with(['patient', 'questionnaire', 'createdBy', 'closedBy'])
        ->orderBy('updated_at', 'desc')->get();


        // تحويل النتائج إلى DataTables وتحديد الأعمدة المطلوبة
        return DataTables::of($questionnaires)
            ->addColumn('id', function ($questionnaire) {
                return $questionnaire->id;
            })
            ->addColumn('action', function ($questionnaire) {
                return $this->AllQuestionnairesActionButtons($questionnaire);
            })
            ->addColumn('questionnair', function ($questionnaire) {
                // يتم جلب اسم الاستبيان من العلاقة (إذا كانت موجودة)، وإلا يتم إرجاع '-'
                return $questionnaire->questionnaire->name_ar . '/' . $questionnaire->questionnaire->name_en;
            })
            ->addColumn('status', function ($questionnaire) {
                return $this->allQuestionnairesStatusBadge($questionnaire);
            })
            ->addColumn('patient_full_name', function ($questionnaire) {
                return $questionnaire->patient->full_name;
            })
            ->addColumn('patient_number', function ($questionnaire) {
                return $questionnaire->patient_id;
            })
            ->addColumn('patientVisit', function ($questionnaire) {
                if ($questionnaire->patientVisit) {
                    return '<span class="badge badge-soft-info badge-border" style="margin: 5px;">
                            تاريخ الزيارة: ' . $questionnaire->patientVisit->date_visit . '
                        </span><br>
                        <span class="badge badge-soft-primary badge-border" style="margin: 5px;">
                            النوع: ' . $questionnaire->patientVisit->type . '
                        </span>
                        <span class="badge badge-soft-warning badge-border" style="margin: 5px;">
                            الحالة: ' . $questionnaire->patientVisit->status . '
                        </span>';
                }
                return '-';
            })

            ->addColumn('created_by', function ($questionnaire) {
                // يتم عرض اسم المستخدم الذي أنشأ السجل
                return $questionnaire->createdBy ? $questionnaire->createdBy->name : '-';
            })
            ->addColumn('closed_by', function ($questionnaire) {
                // يتم عرض اسم المستخدم الذي أغلق السجل (إن وجد)
                return $questionnaire->closedBy ? $questionnaire->closedBy->name : '-';
            })
            ->rawColumns(['action', 'patientVisit', 'status'])
            ->make(true);
    }
    /**
     * Get the status badge for questionnaire
     */
    private function allQuestionnairesStatusBadge($questionnaire)
    {
        $statusLabels = [
            'under_review' => 'bg-dark',
            'closed' => 'bg-primary',
            'new' => 'bg-success',

        ];

        return '<span class="badge ' . ($statusLabels[$questionnaire->status] ?? 'badge-light') . '  badge-border">' . trans('status.' . $questionnaire->status) . '</span>';
    }
    public function show($encryptedId)
    {
        try {
            // فك تشفير الـ id
            $id = Crypt::decryptString($encryptedId);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'معرف غير صالح');
        }

        // جلب سجل الاستبيان الرئيسي مع العلاقات الضرورية
        $questionnaire = adm_b_questionnaire::with([
            'patient',
            'questionnaire',
            'createdBy',
            'closedBy',
            'patientVisit'
        ])->findOrFail($id);

        // جلب تفاصيل الاستبيان (الإجابات، الملاحظات)
        $details = adm_b_questionnaires_details::where('questionnair_id', $questionnaire->id)->get();

        // جلب الأسئلة والإجابات الخاصة بنوع الاستبيان من جدول adm_s_question
        $questions = adm_s_question::with('answerValues')
            ->where('type_id', $questionnaire->questionnair)
            ->get();

        // تمرير البيانات إلى صفحة العرض
        return view('administrative.call_center.program.questionnaires.details', compact('questionnaire', 'details', 'questions'));
    }


    public function updateQuestionnaireStatus(Request $request, $id)
    {
        // التحقق من وجود الاستبيان
        $questionnaire = adm_b_questionnaire::find($id);
        if (!$questionnaire) {
            return response()->json(['error' => 'لم يتم العثور على الاستبيان'], 404);
        }

        // التحقق من أن الحالة الجديدة صحيحة
        $validStatuses = ['new', 'under_review', 'closed'];
        $newStatus = $request->input('status');

        if (!in_array($newStatus, $validStatuses)) {
            return response()->json(['error' => 'حالة غير صالحة'], 400);
        }

        // بيانات التحديث
        $updateData = [
            'status' => $newStatus,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ];

        // إذا كانت الحالة "closed"، احفظ المستخدم في `closed_by`
        if ($newStatus === 'closed') {
            $updateData['closed_by'] = Auth::id();
        }

        // تنفيذ التحديث
        $questionnaire->update($updateData);

        return response()->json(['success' => 'تم تحديث حالة الاستبيان بنجاح']);
    }

    public function getQuestions(Request $request)
    {
        $questionnaireId = $request->questionnaire_id;

        // جلب الأسئلة التي يكون عمود type_id فيها هو رقم الاستبيان المحدد،
        // مع تحميل علاقة الإجابات (answerValues)
        $questions = adm_s_question::with('answerValues')
            ->where('type_id', $questionnaireId)
            ->get();

        return response()->json(['questions' => $questions]);
    }

    public function create()
    {
        return view('administrative.call_center.program.questionnaires.create');
    }

    public function create_questionnaires()
    {

        $questionnaires = adm_s_questionnaires::where('status', true)->get();
        return view('administrative.call_center.program.questionnaires.create_questionnaires', compact('questionnaires'));
    }
    public function edit($encryptedId)
    {
        try {
            // فك تشفير الـ id
            $id = Crypt::decryptString($encryptedId);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'معرف غير صالح');
        }

        // جلب سجل الاستبيان الرئيسي مع العلاقات الضرورية
        $questionnaire = adm_b_questionnaire::with([
            'patient',
            'questionnaire',
            'createdBy',
            'closedBy',
            'patientVisit'
        ])->findOrFail($id);

        // جلب تفاصيل الاستبيان (الإجابات، الملاحظات)
        $details = adm_b_questionnaires_details::where('questionnair_id', $questionnaire->id)->get();

        // جلب الأسئلة والإجابات الخاصة بنوع الاستبيان من جدول adm_s_question
        $questions = adm_s_question::with('answerValues')
            ->where('type_id', $questionnaire->questionnair)
            ->get();

        // تمرير البيانات إلى صفحة التعديل
        return view('administrative.call_center.program.questionnaires.edit', compact('questionnaire', 'details', 'questions'));
    }


    public function update(Request $request, $encryptedId)
    {
        try {
            // فك تشفير الـ id
            $id = Crypt::decryptString($encryptedId);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'معرف غير صالح');
        }

        // جلب سجل الاستبيان الرئيسي
        $questionnaire = adm_b_questionnaire::findOrFail($id);

        // تحقق من صحة البيانات المُرسلة
        $validatedData = $request->validate([
            'answers' => 'required|array',
            'notes'   => 'nullable|array',
        ]);

        // لتحليل نوع السؤال، نقوم بتحميل كل الأسئلة الخاصة بنوع الاستبيان الحالي
        $questions = adm_s_question::where('type_id', $questionnaire->questionnair)
            ->get()
            ->keyBy('id'); // بحيث يكون المفتاح هو question id

        // استخدام معاملة قاعدة بيانات لضمان التحديث المتكامل
        DB::transaction(function () use ($validatedData, $questionnaire, $questions) {
            // تحديث بيانات الاستبيان الرئيسي (يمكنك تحديث حقول إضافية هنا إذا لزم الأمر)
            $questionnaire->update([
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            // لكل سؤال في الإجابات المرسلة
            foreach ($validatedData['answers'] as $questionId => $answerValue) {
                $detailData = [
                    'notes'      => $validatedData['notes'][$questionId] ?? null,
                    'updated_by' => auth()->id(),
                ];

                // إذا كان السؤال موجوداً في قاعدة البيانات
                if (isset($questions[$questionId])) {
                    $question = $questions[$questionId];

                    // إذا كان السؤال يتوقع إجابة نصية حرة
                    // (أي لا يوجد خيار مُعرف في قاعدة البيانات؛ و/أو خاصية text=true أو 1)
                    if ($question->answer_id === null && ($question->text == true || $question->text == 1)) {
                        $detailData['answer_id'] = null;
                        $detailData['text']      = $answerValue;
                    } else {
                        // السؤال اختيار من متعدد: يجب أن يكون الجواب رقميًا (يُخزن في answer_id)
                        $detailData['answer_id'] = $answerValue;
                        $detailData['text']      = null;
                    }
                } else {
                    // إذا لم يتم العثور على السؤال، يمكنك تخطيه أو التعامل معه
                    $detailData['answer_id'] = null;
                    $detailData['text']      = $answerValue;
                }

                // تحديث أو إنشاء سجل تفاصيل الاستبيان
                adm_b_questionnaires_details::updateOrCreate(
                    [
                        'questionnair_id' => $questionnaire->id,
                        'question_id'     => $questionId,
                    ],
                    $detailData
                );
            }
        });

        return redirect()->route('all_questionnaires.index')
            ->with('success', 'تم تحديث الاستبيان بنجاح.');
    }

    public function checkQuestionnaire(Request $request)
    {
        $patient_id = $request->patient_id;
        $questionnaire_id = $request->questionnaire_id;
        $visit_id = $request->visit_id;

        // البحث عن سجل موجود لنفس المريض ونفس الاستبيان في نفس الزيارة
        $existing = adm_b_questionnaire::where('patient_id', $patient_id)
            ->where('questionnair', $questionnaire_id)
            ->where('patient_visits_id', $visit_id)
            ->first();

        return response()->json(['exists' => ($existing ? true : false)]);
    }

    private function isPredefinedAnswer($value)
    {
        return adm_s_answer_values::where('id', $value)->exists();
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات الأساسية
        $validatedData = $request->validate([
            'patient_id'       => 'required|integer',
            'visit_id'         => 'required|integer',
            'questionnaire_id' => 'required|integer',
            'answers'          => 'required|array',
            'answers.*'        => 'required', // كل إجابة مطلوبة
            // "notes" هي مصفوفة اختيارية
        ]);

        // إنشاء سجل الاستبيان الرئيسي
        $questionnaireRecord = adm_b_questionnaire::create([
            'patient_id'       => $request->patient_id,
            'patient_visits_id' => $request->visit_id,
            'questionnair'     => $request->questionnaire_id,
            'status'           => 'new', // الحالة الافتراضية
            'created_by'       => auth()->id(), // نفترض أن المستخدم مسجل الدخول
            'closed_by'        => null,
        ]);

        // جلب الإجابات والملاحظات المرسلة من النموذج
        $answers = $request->input('answers', []);
        $notes   = $request->input('notes', []); // مصفوفة الملاحظات اختيارية

        // إنشاء سجلات تفاصيل الاستبيان لكل سؤال
        foreach ($answers as $questionId => $answerValue) {
            $detailData = [
                'questionnair_id' => $questionnaireRecord->id, // معرف سجل الاستبيان الرئيسي
                'question_id'     => $questionId,
            ];
            if (isset($notes[$questionId]) && !empty($notes[$questionId])) {
                $detailData['answer_id'] = is_numeric($answerValue) ? $answerValue : null;
                $detailData['notes']     = $notes[$questionId];
                $detailData['text']      = is_numeric($answerValue) ? null : $answerValue;
            } else {
                if (is_numeric($answerValue) && $this->isPredefinedAnswer($answerValue)) {
                    // إذا كانت الإجابة رقمية ومن ضمن الإجابات المحددة مسبقًا، يتم حفظها في answer_id
                    $detailData['answer_id'] = $answerValue;
                    $detailData['notes']     = null;
                    $detailData['text']      = null;
                } else {
                    // حفظ القيم الرقمية كإجابات نصية إذا لم تكن من الإجابات المحددة مسبقًا
                    $detailData['answer_id'] = null;
                    $detailData['notes']     = null;
                    $detailData['text'] = is_string($answerValue) ? preg_replace('/^text_/', '', $answerValue) : null;
                }
            }

            adm_b_questionnaires_details::create($detailData);
        }

        return redirect()->route('all_questionnaires.index')
            ->with('success', 'تم حفظ الاستبيان بنجاح');
    }


    public function destroy($encryptedId)
    {
        try {
            // فك تشفير الـ id
            $id = Crypt::decryptString($encryptedId);
        } catch (Exception $e) {
            // إذا فشل التشفير، يمكن إعادة توجيه مع رسالة خطأ
            return redirect()->back()->with('error', 'معرف غير صالح');
        }

        $questionnaire = adm_b_questionnaire::findOrFail($id);

        try {
            $questionnaire->delete();
            return redirect()->route('all_questionnaires.index')
                ->with('success', trans('call_center_trans.questionnaire_deleted'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', trans('call_center_trans.error_deleting_questionnaire'));
        }
    }
    public function uploadAttachments($id)
    {
        try {
            // فك تشفير الـ ID
            $decryptedId = Crypt::decryptString($id);

            // جلب بيانات الاستبيان
            $questionnaire = adm_b_questionnaire::findOrFail($decryptedId);

            // إرجاع الصفحة مع بيانات الاستبيان
            return view('administrative.call_center.program.questionnaires.upload_attachments', compact('questionnaire'));

        } catch (DecryptException $e) {
            // في حالة فشل فك التشفير، يمكن إعادة التوجيه مع رسالة خطأ
            return redirect()->route('all_questionnaires.index')->with('error', 'حدث خطأ أثناء محاولة فتح المرفقات.');
        }
    }






    public function storeAttachments(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $questionnaire = adm_b_questionnaire::findOrFail($decryptedId);

            // التحقق من صحة الملفات
            $validator = Validator::make($request->all(), [
                'attachments.*' => ['required', 'file','max:5120']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ: نوع الملف غير مدعوم'
                ], 400);
            }

            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');

                $year = date('Y');
                $month = date('m');
                $storagePath = "questionnaires/{$year}/{$month}";

                $existingAttachments = json_decode($questionnaire->attachments, true) ?? [];
                $newAttachments = [];

                foreach ($files as $file) {
                    // إنشاء اسم آمن للملف
                    $safeFileName = preg_replace('/[^A-Za-z0-9\-_.]/', '', $file->getClientOriginalName());
                    $fileName = time() . '_' . uniqid() . '_' . $safeFileName;

                    // رفع الملف
                    try {
                        $path = $file->storeAs($storagePath, $fileName, 'public');
                        $fullPath = asset("storage/{$path}"); // إنشاء رابط يمكن الوصول إليه
                        $existingAttachments[] = $path;
                        $newAttachments[] = $fullPath;
                    } catch (Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل رفع الملف: ' . $e->getMessage()
                        ], 500);
                    }
                }

                // تحديث المرفقات في قاعدة البيانات
                $questionnaire->update([
                    'attachments' => json_encode($existingAttachments, JSON_UNESCAPED_SLASHES)
                ]);

                // إرجاع المسارات الجديدة للاستعمال في تحديث الواجهة الأمامية
                return response()->json([
                    'success' => true,
                    'message' => 'تم الرفع بنجاح',
                    'files' => $newAttachments
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم اختيار ملفات'
                ], 400);
            }
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في فك التشفير: ' . $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()
            ], 500);
        }
    }

public function deleteAttachment(Request $request)
{
    try {
        $decryptedId = Crypt::decryptString($request->questionnaire_id);
        $questionnaire = adm_b_questionnaire::findOrFail($decryptedId);

        $fileToDelete = $request->file;
        $attachments = json_decode($questionnaire->attachments, true) ?? []; // التعديل هنا

        // البحث عن الملف في المصفوفة
        $fileIndex = array_search($fileToDelete, $attachments);

        if ($fileIndex !== false) {
            // حذف الملف من السيرفر
            if (Storage::disk('public')->exists($fileToDelete)) { // استخدام Storage
                Storage::disk('public')->delete($fileToDelete);
            }

            // إزالة الملف من المصفوفة
            unset($attachments[$fileIndex]);

            // إعادة ترتيب المفاتيح وتحديث قاعدة البيانات
            $questionnaire->update([
                'attachments' => json_encode(array_values($attachments))
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'الملف غير موجود'], 404);

    } catch (Exception $e) {
        return response()->json([
            'error' => 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage()
        ], 500);
    }
}
public function daily_questionnaires(){
    return view('administrative.call_center.program.questionnaires.daily_questionnaires');
}

public function getTodaysVisits(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $query = reservations::with(['patient', 'clinic', 'doctor', 'patient_visits', 'user'])
        ->whereDate('date_reservations', $today)
        ->whereNotExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('adm_b_questionnaires')
              ->whereColumn('adm_b_questionnaires.patient_visits_id', 'adm_s_reservations.visit_id')
              ->whereNull('adm_b_questionnaires.deleted_at');
        })
        ->select('adm_s_reservations.*', DB::raw('adm_s_reservations.status as reservation_status'));

        return DataTables::of($query)
            ->addColumn('patient_name', fn($row) => optional($row->patient)->full_name ?? '-')
            ->addColumn('clinic_name', function($row) {
                $clinic = $row->clinic;
                if (! $clinic) {
                    return '-';
                }
                $ar = $clinic->name_ar  ?? '-';
                $en = $clinic->name_en  ?? '-';
                return "{$ar} / {$en}";
            })
            ->addColumn('doctor_name',  fn($row) => optional($row->doctor)->full_name ?? '-')
            ->addColumn('visit_type', function($row) {
                $type = optional($row->patient_visits)->type;
                return $type
                    ? trans("call_center_trans.{$type}")
                    : '-';
            })            ->addColumn('created_by',   fn($row) => optional($row->user)->name ?? '-')
            ->addColumn('status', function($row) {
                $status = $row->reservation_status;
                return $status
                    ? trans("call_center_trans.{$status}")
                    : '-';
            })            ->addColumn('action', function($row) {
                $url = route('create_questionnaires', [
                    'patient_id' => $row->id_patient,
                    'visit_id'   => $row->visit_id,
                ]);
                return '<a class="btn btn-sm btn-primary" href="' . $url . '">'
                       . trans('call_center_trans.create_questionaire') .
                       '</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}