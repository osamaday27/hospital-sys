<?php

namespace App\Http\Controllers\administrative\call_center;

use App\models\hr\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\models\administrative\patient_inquiry;
use App\models\administrative\medical_record; // patient

class patient_inquiriescontroller extends Controller
{
    // قائمة
    public function index(Request $request)
    {
        // الصفحة نفسها هتستدعي داتا الجدول عبر AJAX إلى datatable()
        return view('administrative.call_center.program.patient_inquiries.index');
    }

    // إنشاء
    public function create(Request $request)
    {
        $prefill = [
            'is_registered' => $request->boolean('registered', false) ? 1 : 0,
            'patient_id'    => $request->get('patient_id'),
            'full_name'     => $request->get('name'),
            'gender'        => $request->get('gender'),
            'age'           => $request->get('age'),
            'inquiry_date'  => $request->get('inquiry_date'),
        ];

        $patient = null;
        if ($prefill['is_registered'] && $prefill['patient_id']) {
            $patient = medical_record::find($prefill['patient_id']);
        }

        return view('administrative.call_center.program.patient_inquiries.create', compact('prefill','patient'));
    }

    // حفظ جديد
    public function store(Request $request)
    {
        $isRegistered = (int)$request->input('is_registered', 0);

        $baseRules = [
            'is_registered' => ['required', Rule::in([0,1])],
            'inquiry_date'  => ['required','date'],
            'inquiry'       => ['required','string','min:5'],
            'doctor_id'     => ['required','integer','exists:' . (new Employee)->getTable() . ',id'],
            'contact_type'  => ['required','array','min:1'],
            'contact_value' => ['required','array','min:1'],
        ];

        if ($isRegistered === 1) {
            $baseRules['patient_id'] = ['required','integer','exists:' . (new medical_record)->getTable() . ',id'];
        } else {
            $baseRules['full_name'] = ['required','string','min:2','max:150'];
            $baseRules['gender']    = ['required', Rule::in(['male','female'])];
            $baseRules['age']       = ['required','integer','min:0','max:120'];
        }

        $validated = $request->validate($baseRules, [
            'contact_type.min'  => trans('call_center_trans.validation_at_least_one_contact'),
            'contact_value.min' => trans('call_center_trans.validation_at_least_one_contact'),
        ]);

        $types  = $request->input('contact_type', []);
        $values = $request->input('contact_value', []);
        $contacts = [];
        foreach ($types as $i => $t) {
            $val = $values[$i] ?? null;
            if ($t && $val) $contacts[] = ['type'=>(string)$t,'value'=>(string)$val];
        }
        if (empty($contacts)) {
            return back()->withInput()->with('error', trans('call_center_trans.validation_at_least_one_contact'));
        }

        patient_inquiry::create([
            'is_registered'   => $isRegistered,
            'patient_id'      => $isRegistered ? (int)$request->patient_id : null,
            'full_name'       => $isRegistered ? null : $request->full_name,
            'gender'          => $isRegistered ? null : $request->gender,
            'age'             => $isRegistered ? null : (int)$request->age,
            'inquiry_date'    => $request->inquiry_date,
            'inquiry'         => $request->inquiry,
            'status'          => 'new',
            'doctor_id'       => (int)$request->doctor_id,
            'referral_entity' => $request->referral_entity,
            'heard_about_us'  => $request->heard_about_us,
            'notes'           => $request->notes,
            'contact_methods' => $contacts,
            'created_by'      => Auth::id(),
        ]);

        return redirect()->route('patient_inquiries.index')
            ->with('success', trans('call_center_trans.created_success'));
    }

    // تعديل
    public function edit($id)
    {
        $inquiry = patient_inquiry::findOrFail($id);

        if ($inquiry->status === 'reviewed') {
            return redirect()->route('patient_inquiries.index')->with('error', trans('call_center_trans.cannot_edit_reviewed'));
        }

        $patient = $inquiry->is_registered ? $inquiry->patient : null;
        return view('administrative.call_center.program.patient_inquiries.edit', compact('inquiry','patient'));
    }

    // حفظ التعديل
    public function update(Request $request, $id)
    {
        $inquiry = patient_inquiry::findOrFail($id);
        if ($inquiry->status === 'reviewed') {
            return redirect()->route('patient_inquiries.index')->with('error', trans('call_center_trans.cannot_edit_reviewed'));
        }

        $isRegistered = (int)$inquiry->is_registered;

        $baseRules = [
            'inquiry_date'  => ['required','date'],
            'inquiry'       => ['required','string','min:5'],
            'doctor_id'     => ['required','integer','exists:' . (new Employee)->getTable() . ',id'],
            'contact_type'  => ['required','array','min:1'],
            'contact_value' => ['required','array','min:1'],
        ];

        if ($isRegistered === 0) {
            $baseRules['full_name'] = ['required','string','min:2','max:150'];
            $baseRules['gender']    = ['required', Rule::in(['male','female'])];
            $baseRules['age']       = ['required','integer','min:0','max:120'];
        }

        $validated = $request->validate($baseRules, [
            'contact_type.min'  => trans('call_center_trans.validation_at_least_one_contact'),
            'contact_value.min' => trans('call_center_trans.validation_at_least_one_contact'),
        ]);

        $types  = $request->input('contact_type', []);
        $values = $request->input('contact_value', []);
        $contacts = [];
        foreach ($types as $i => $t) {
            $val = $values[$i] ?? null;
            if ($t && $val) $contacts[] = ['type'=>(string)$t,'value'=>(string)$val];
        }
        if (empty($contacts)) {
            return back()->withInput()->with('error', trans('call_center_trans.validation_at_least_one_contact'));
        }

        $inquiry->inquiry_date    = $request->inquiry_date;
        $inquiry->inquiry         = $request->inquiry;
        $inquiry->doctor_id       = (int)$request->doctor_id;
        $inquiry->referral_entity = $request->referral_entity;
        $inquiry->heard_about_us  = $request->heard_about_us;
        $inquiry->notes           = $request->notes;
        $inquiry->contact_methods = $contacts;

        if ($isRegistered === 0) {
            $inquiry->full_name = $request->full_name;
            $inquiry->gender    = $request->gender;
            $inquiry->age       = (int)$request->age;
        }

        $inquiry->save();

        return redirect()->route('patient_inquiries.index')
            ->with('success', trans('call_center_trans.updated_success'));
    }

    // حذف
    public function destroy($id)
    {
        $inquiry = patient_inquiry::findOrFail($id);
        $inquiry->delete();
        return redirect()->route('patient_inquiries.index')
            ->with('success', trans('call_center_trans.deleted_success'));
    }

    // تغيير الحالة
    public function review($id)
    {
        $inquiry = patient_inquiry::findOrFail($id);
        if ($inquiry->status !== 'reviewed') {
            $inquiry->status = 'reviewed';
            $inquiry->save();
        }
        return back()->with('success', trans('call_center_trans.status_updated'));
    }

    // مودال التفاصيل (جزء HTML يُعرض داخل مودال في index)
    public function modal($id)
    {
        $inquiry = patient_inquiry::with(['patient','doctor','creator'])->findOrFail($id);
        return view('administrative.call_center.program.patient_inquiries._show_modal', compact('inquiry'));
    }

    // AJAX: بحث المرضى (Select2)
    public function ajaxPatients(Request $request)
    {
        $term = $request->get('q', '');
        $q = medical_record::query();
        $q->search($term)->limit(20);

        $results = $q->get()->map(function ($p) {
            $text = trim(($p->full_name ?? '')) . ' - ' . ($p->medical_number ?? '');
            return ['id' => $p->id, 'text' => $text === ' - ' ? (string)$p->id : $text];
        });

        return response()->json(['results' => $results]);
    }

    // AJAX: بحث الأطباء (Select2)
    public function ajaxDoctors(Request $request)
    {
        $term = $request->get('q', '');
        $q = Employee::query();
        if ($term) $q->where('full_name','like','%'.$term.'%');
        $results = $q->orderBy('full_name')->limit(20)->get()
            ->map(fn($d)=>['id'=>$d->id,'text'=>$d->full_name]);

        return response()->json(['results' => $results]);
    }

    // مصدر DataTables (Server-side) مع حماية وتنسيق تاريخ آمن
    public function datatable(Request $request)
    {
        try {
            $draw   = (int)$request->get('draw', 1);
            $start  = (int)$request->get('start', 0);
            $length = (int)$request->get('length', 10);
            $search = trim((string)($request->input('search.value') ?? ''));
            $status = $request->get('status');

            $base = patient_inquiry::query()->with(['patient','doctor','creator']);

            $totalAll = (clone $base)->count();

            if ($status) {
                $base->where('status', $status);
            }

            if ($search !== '') {
                $like = '%' . str_replace('%','\%',$search) . '%';
                $base->where(function($q) use ($like) {
                    $q->orWhere('full_name','like',$like)
                      ->orWhere('inquiry','like',$like)
                      ->orWhere('referral_entity','like',$like)
                      ->orWhere('heard_about_us','like',$like)
                      ->orWhereHas('patient', function($qq) use ($like){
                          $qq->where('full_name','like',$like)
                             ->orWhere('medical_number','like',$like);
                      })
                      ->orWhereHas('creator', function($qq) use ($like){
                          $qq->where('name','like',$like);
                      })
                      ->orWhereHas('doctor', function($qq) use ($like){
                          $qq->where('full_name','like',$like);
                      });
                });
            }

            $filtered = (clone $base)->count();

            // ⚠️ خريطة الترتيب الصحيحة حسب أعمدة الجدول في الـ index:
            // 0: sr_no | 1: action | 2: patient_name | 3: inquiry_date | 4: status | 5: created_by
            $orderColIndex = (int)($request->input('order.0.column') ?? 0);
            $orderDir      = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
            $orderMap = [
                0 => 'id',
                3 => 'inquiry_date',
                4 => 'status',
                5 => 'created_by',
            ];
            $orderBy = $orderMap[$orderColIndex] ?? 'id';
            $base->orderBy($orderBy, $orderDir);

            $rows = $base->skip($start)->take($length)->get();

            $data = [];
            foreach ($rows as $idx => $row) {
                $sr = $start + $idx + 1;

                $patientText = $row->is_registered && $row->patient
                    ? ($row->patient->full_name . ' (' . ($row->patient->medical_number ?? '') . ')')
                    : (string)($row->full_name ?? '');

                // تنسيق آمن للتاريخ
                $inquiryDate = null;
                if (!empty($row->inquiry_date)) {
                    try {
                        $inquiryDate = \Illuminate\Support\Carbon::parse($row->inquiry_date)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $inquiryDate = (string)$row->inquiry_date; // fallback
                    }
                }

                $statusBadge = $row->status === 'new'
                    ? '<span class="badge bg-info">'.e(trans('call_center_trans.status_new')).'</span>'
                    : '<span class="badge bg-success">'.e(trans('call_center_trans.status_reviewed')).'</span>';

                $creatorName = optional($row->creator)->name;

                $showBtn = '<button class="btn btn-sm btn-outline-secondary"
                            onclick="openDetailsModalById('.$row->id.')">
                            <i class="fa fa-eye"></i> '.e(trans('general.show')).'</button>';

                $editBtn = '';
                $reviewForm = '';
                if ($row->status === 'new') {
                    $editBtn = '<a href="'.route('patient_inquiries.edit',$row->id).'"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-edit"></i> '.e(trans('general.edit')).'</a>';

                    $reviewForm = '<form method="POST" action="'.route('patient_inquiries.review',$row->id).'"
                                    class="d-inline">'.csrf_field().method_field('PATCH').'
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-check"></i> '.e(trans('call_center_trans.mark_reviewed')).'</button>
                                  </form>';
                }

                $delBtn = '<button class="btn btn-sm btn-outline-danger"
                           onclick="deleteInquiry('.$row->id.')">
                           <i class="fa fa-trash"></i> '.e(trans('general.delete')).'</button>';

                $actions = $showBtn.' '.$editBtn.' '.$reviewForm.' '.$delBtn;

                $data[] = [
                    'sr_no'        => $sr,
                    'action'       => $actions,
                    'patient_name' => e($patientText),
                    'inquiry_date' => e($inquiryDate),
                    'status'       => $statusBadge, // HTML badge
                    'created_by'   => e($creatorName),
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalAll,
                'recordsFiltered' => $filtered,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('PatientInquiries datatable error: '.$e->getMessage().' @'.$e->getFile().':'.$e->getLine());
            return response()->json([
                'draw' => (int)$request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error',
            ], 200);
        }
    }

    /**
     * دمج العرض داخل index كمودال:
     * لو اتنادى show مباشرة، هنرجّع نفس الـ Blade الجزئي للمودال لو الطلب AJAX،
     * غير كده نرجّع للمؤشر.
     */
    public function show($id)
    {
        $inquiry = patient_inquiry::with(['patient','doctor','creator'])->findOrFail($id);
        if (request()->ajax() || request()->wantsJson()) {
            return view('administrative.call_center.program.patient_inquiries._show_modal', compact('inquiry'));
        }
        return redirect()->route('patient_inquiries.index');
    }
}
