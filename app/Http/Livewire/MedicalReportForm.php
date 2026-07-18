<?php

namespace App\Http\Livewire;

use App\models\medical\m_d_medicalreport;
use App\models\medical\m_d_medicalreporttemplate;
use App\models\medical\mbdiseaseshispat;
use App\models\medical\mbcomplaintshispat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\models\administrative\medical_record;
use App\models\administrative\patient_visits;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class medicalReportForm extends Component
{
    // معلومات الزيارة والمريض
    public $id_patient;
    public $id_reservation;
    public $id_visit;
    public $type_visit;
    
    // محتوى التقرير
    public $content = '';
    public $report_name;
    public $template_id = null;
    public $notes = ''; // ملاحظات إضافية
    public $report_type; // نوع التقرير (أشعة، مختبر، عام، إلخ)
    
    // البيانات المرجعية
    public $templates = [];
    public $previousReports = [];
    public $diagnoses = []; // التشخيصات المتاحة
    public $complaints = []; // الشكاوى المتاحة
    
    // بيانات المريض
    public $patient_name;
    public $patient_dob;
    public $patient_age;
    public $patient_gender;
    public $patient_phone;
    public $patient_national_id;
    
    // بيانات الزيارة
    public $visit_date;
    public $visit_time;
    public $visit_type_name;
    
    // بيانات الطبيب
    public $doctor_name;
    public $doctor_specialty;
    public $doctor_license;
    
    // تاريخ التقرير
    public $report_date;
    public $report_time;
    
    // حالة التقرير
    public $report_status = 'draft'; // draft, completed, signed, archived
    
    // متغيرات التحكم
    public $editingReportId = null;
    public $showDeleteConfirm = false;
    public $reportToDelete = null;
    public $showPreview = false;
    public $previewContent = '';
    
    // البحث والفلترة
    public $searchReports = '';
    public $filterByType = '';
    public $filterByStatus = '';
    public $filterByDate = '';
    
    // إعدادات الطباعة
    public $includeLogo = true;
    public $includeWatermark = false;
    public $includeSignature = true;
    
    // متغيرات للتحليلات
    public $reportsCount = 0;
    public $lastReportDate = null;

    protected $listeners = [
        'contentUpdated' => 'updateContent',
        'templateSelected' => 'loadTemplate',
        'refreshReports' => 'loadPreviousReports'
    ];

    protected $rules = [
        'report_name' => 'required|string|max:255|min:3',
        'content' => 'required|string|min:20',
        'template_id' => 'nullable|exists:m_d_medicalreporttemplates,id',
        'notes' => 'nullable|string|max:1000',
        'report_type' => 'nullable|string|max:100',
        'report_status' => 'required|in:draft,completed,signed,archived',
    ];

    protected $validationAttributes = [
        'report_name' => 'اسم التقرير',
        'content' => 'محتوى التقرير',
        'notes' => 'الملاحظات',
        'report_type' => 'نوع التقرير',
    ];

    protected $messages = [
        'report_name.required' => 'يجب إدخال اسم التقرير',
        'report_name.min' => 'اسم التقرير يجب أن يكون 3 أحرف على الأقل',
        'content.required' => 'يجب إدخال محتوى التقرير',
        'content.min' => 'محتوى التقرير قصير جداً',
    ];

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;

        $this->initializePatientData();
        $this->initializeVisitData();
        $this->initializeDoctorData();
        $this->loadTemplates();
        $this->loadMedicalData();
        $this->loadPreviousReports();
        $this->loadLatestReport();
        $this->calculateStatistics();

        // تعيين التواريخ الحالية
        $this->report_date = now()->format('Y-m-d');
        $this->report_time = now()->format('H:i');
    }

    private function initializePatientData()
    {
        $patient = medical_record::find($this->id_patient);
        
        if ($patient) {
            $this->patient_name = $patient->full_name;
            $this->patient_dob = $patient->date_of_birth;
            $this->patient_age = Carbon::parse($patient->date_of_birth)->age;
            $this->patient_gender = $patient->gender ?? 'غير محدد';
            $this->patient_phone = $patient->phone ?? '';
            $this->patient_national_id = $patient->national_id ?? '';
        }
    }

    private function initializeVisitData()
    {
        $visit = patient_visits::find($this->id_visit);
        
        if ($visit) {
            $visitDateTime = Carbon::parse($visit->date_visit);
            $this->visit_date = $visitDateTime->format('Y-m-d');
            $this->visit_time = $visitDateTime->format('H:i');
            $this->visit_type_name = $visit->visitType->name ?? $this->type_visit;
        } else {
            $this->visit_date = now()->format('Y-m-d');
            $this->visit_time = now()->format('H:i');
            $this->visit_type_name = $this->type_visit;
        }
    }

    private function initializeDoctorData()
    {
        $user = Auth::user();
        $this->doctor_name = $user->name;
        $this->doctor_specialty = $user->specialty->name ?? 'غير محدد';
        $this->doctor_license = $user->license_number ?? '';
    }

    private function loadTemplates()
    {
        $user = Auth::user();
        
        $this->templates = m_d_medicalreporttemplate::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->where('is_global', true)
                      ->orWhere('user_id', $user->id)
                      ->orWhereHas('users', fn($q) => $q->where('users.id', $user->id))
                      ->orWhere('specialty_id', $user->specialty_id);
            })
            ->orderBy('name')
            ->get();
    }

    private function loadMedicalData()
    {
        // تحميل التشخيصات للمريض
        $this->diagnoses = mbdiseaseshispat::where('id_patient', $this->id_patient)
            ->where('id_visit', $this->id_visit)
            ->latest()
            ->get();

        // تحميل الشكاوى للمريض
        $this->complaints = mbcomplaintshispat::where('id_patient', $this->id_patient)
            ->where('id_visit', $this->id_visit)
            ->latest()
            ->get();
    }

    private function loadLatestReport()
    {
        $latest = m_d_medicalreport::where('id_patient', $this->id_patient)
            ->where('id_visit', $this->id_visit)
            ->latest('report_date')
            ->first();

        if ($latest) {
            $this->editingReportId = $latest->id;
            $this->content = $latest->content;
            $this->template_id = $latest->template_id;
            $this->report_name = $latest->report_name;
            $this->notes = $latest->notes ?? '';
            $this->report_type = $latest->report_type ?? '';
            $this->report_status = $latest->status ?? 'draft';
        }
    }

    private function calculateStatistics()
    {
        $reports = m_d_medicalreport::where('id_patient', $this->id_patient)->get();
        
        $this->reportsCount = $reports->count();
        $this->lastReportDate = $reports->max('report_date');
    }

    public function updatedTemplateId($value)
    {
        if (!$value) {
            return;
        }

        $template = m_d_medicalreporttemplate::find($value);
        
        if ($template) {
            $this->content = $this->parseTemplate($template->content);
            $this->report_type = $template->category ?? '';
            
            if (!$this->report_name) {
                $this->report_name = $template->name . ' - ' . $this->patient_name;
            }
            
            $this->dispatchBrowserEvent('setEditorContent', ['content' => $this->content]);
            
            session()->flash('info', 'تم تحميل القالب بنجاح');
        }
    }

    private function parseTemplate($content)
    {
        // استبدال المتغيرات في القالب
        $replacements = [
            '{{patient_name}}' => $this->patient_name,
            '{{patient_age}}' => $this->patient_age . ' سنة',
            '{{patient_gender}}' => $this->patient_gender,
            '{{visit_date}}' => Carbon::parse($this->visit_date)->format('Y/m/d'),
            '{{doctor_name}}' => $this->doctor_name,
            '{{doctor_specialty}}' => $this->doctor_specialty,
            '{{current_date}}' => now()->format('Y/m/d'),
            '{{current_time}}' => now()->format('H:i'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function insertComplaint()
    {
        if ($this->complaints->isEmpty()) {
            session()->flash('warning', 'لا توجد شكاوى مسجلة لهذه الزيارة');
            return;
        }

        $complaintText = "\n\n<h3>الشكوى الرئيسية:</h3>\n<ul>\n";
        
        foreach ($this->complaints as $complaint) {
            $complaintText .= "<li>" . $complaint->complaint_text . "</li>\n";
        }
        
        $complaintText .= "</ul>\n";

        $this->content .= $complaintText;
        $this->dispatchBrowserEvent('appendToEditor', ['content' => $complaintText]);
        
        session()->flash('success', 'تم إدراج الشكاوى بنجاح');
    }

    public function insertDiagnosis()
    {
        if ($this->diagnoses->isEmpty()) {
            session()->flash('warning', 'لا توجد تشخيصات مسجلة لهذه الزيارة');
            return;
        }

        $diagnosisText = "\n\n<h3>التشخيص:</h3>\n<ul>\n";
        
        foreach ($this->diagnoses as $diagnosis) {
            $diagnosisText .= "<li>" . $diagnosis->diagnosis_name . "</li>\n";
            
            if ($diagnosis->notes) {
                $diagnosisText .= "<p style='margin-right: 20px;'><em>" . $diagnosis->notes . "</em></p>\n";
            }
        }
        
        $diagnosisText .= "</ul>\n";

        $this->content .= $diagnosisText;
        $this->dispatchBrowserEvent('appendToEditor', ['content' => $diagnosisText]);
        
        session()->flash('success', 'تم إدراج التشخيصات بنجاح');
    }

    public function updateContent($content)
    {
        $this->content = $content;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'id_patient' => $this->id_patient,
                'id_reservation' => $this->id_reservation,
                'id_visit' => $this->id_visit,
                'type_visit' => $this->type_visit,
                'doctor_id' => Auth::id(),
                'content' => $this->content,
                'report_name' => trim($this->report_name),
                'notes' => $this->notes,
                'report_type' => $this->report_type,
                'report_date' => $this->report_date . ' ' . $this->report_time,
                'status' => $this->report_status,
                'template_id' => $this->template_id,
                'updated_at' => now(),
            ];

            if ($this->editingReportId) {
                $report = m_d_medicalreport::findOrFail($this->editingReportId);
                $report->update($data);
                $message = 'تم تحديث التقرير بنجاح';
            } else {
                $data['created_at'] = now();
                $report = m_d_medicalreport::create($data);
                $this->editingReportId = $report->id;
                $message = 'تم حفظ التقرير بنجاح';
            }

            DB::commit();

            $this->loadPreviousReports();
            $this->calculateStatistics();
            
            session()->flash('message', $message);
            $this->dispatchBrowserEvent('report-saved', [
                'message' => $message,
                'reportId' => $report->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save', 'حدث خطأ أثناء حفظ التقرير: ' . $e->getMessage());
        }
    }

    public function saveAndComplete()
    {
        $this->report_status = 'completed';
        $this->save();
    }

    public function saveAsDraft()
    {
        $this->report_status = 'draft';
        $this->save();
    }

    public function signReport()
    {
        if (!$this->editingReportId) {
            session()->flash('error', 'يجب حفظ التقرير أولاً');
            return;
        }

        $report = m_d_medicalreport::find($this->editingReportId);
        
        if ($report) {
            $report->update([
                'status' => 'signed',
                'signed_at' => now(),
                'signed_by' => Auth::id(),
            ]);

            $this->report_status = 'signed';
            session()->flash('message', 'تم توقيع التقرير بنجاح');
            $this->loadPreviousReports();
        }
    }

    public function loadPreviousReport($reportId)
    {
        try {
            $report = m_d_medicalreport::with('template')->findOrFail($reportId);

            $this->editingReportId = $report->id;
            $this->content = $report->content;
            $this->template_id = $report->template_id;
            $this->report_name = $report->report_name;
            $this->notes = $report->notes ?? '';
            $this->report_type = $report->report_type ?? '';
            $this->report_status = $report->status ?? 'draft';

            $this->dispatchBrowserEvent('setEditorContent', ['content' => $this->content]);
            $this->dispatchBrowserEvent('scroll-to-editor');
            
            session()->flash('info', 'تم تحميل التقرير');
            
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء تحميل التقرير');
        }
    }

    public function duplicateReport($reportId)
    {
        try {
            $report = m_d_medicalreport::findOrFail($reportId);

            $this->editingReportId = null;
            $this->content = $report->content;
            $this->template_id = $report->template_id;
            $this->report_name = $report->report_name . ' (نسخة)';
            $this->notes = $report->notes ?? '';
            $this->report_type = $report->report_type ?? '';
            $this->report_status = 'draft';

            $this->dispatchBrowserEvent('setEditorContent', ['content' => $this->content]);
            
            session()->flash('info', 'تم نسخ التقرير. قم بالتعديل والحفظ');
            
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء نسخ التقرير');
        }
    }

    public function confirmDelete($reportId)
    {
        $this->reportToDelete = $reportId;
        $this->showDeleteConfirm = true;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function deleteReport()
    {
        try {
            if (!$this->reportToDelete) {
                return;
            }

            $report = m_d_medicalreport::findOrFail($this->reportToDelete);

            // لا يمكن حذف التقارير الموقعة
            if ($report->status === 'signed') {
                session()->flash('error', 'لا يمكن حذف التقارير الموقعة');
                return;
            }

            $report->delete();

            $this->reportToDelete = null;
            $this->showDeleteConfirm = false;
            $this->loadPreviousReports();
            $this->calculateStatistics();
            
            // إذا تم حذف التقرير الحالي، إعادة تعيين النموذج
            if ($this->editingReportId == $report->id) {
                $this->createNewReport();
            }
            
            session()->flash('message', 'تم حذف التقرير بنجاح');
            $this->dispatchBrowserEvent('report-deleted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء حذف التقرير');
        }
    }

    public function createNewReport()
    {
        $this->reset([
            'template_id',
            'content',
            'report_name',
            'notes',
            'report_type',
            'editingReportId'
        ]);
        
        $this->report_status = 'draft';
        $this->report_date = now()->format('Y-m-d');
        $this->report_time = now()->format('H:i');
        
        $this->dispatchBrowserEvent('setEditorContent', ['content' => '']);
        $this->resetErrorBag();
        $this->resetValidation();
        
        session()->flash('info', 'تم إنشاء تقرير جديد');
    }

    public function previewReport($reportId = null)
    {
        $content = $reportId 
            ? m_d_medicalreport::findOrFail($reportId)->content 
            : $this->content;

        $this->previewContent = $this->generatePrintableReport($content);
        $this->showPreview = true;
        
        $this->dispatchBrowserEvent('show-preview-modal', [
            'content' => $this->previewContent
        ]);
    }

    private function generatePrintableReport($content)
    {
        $header = $this->generateReportHeader();
        $footer = $this->generateReportFooter();
        
        return $header . $content . $footer;
    }

    private function generateReportHeader()
    {
        return "
        <div style='text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px;'>
            " . ($this->includeLogo ? "<img src='/images/logo.png' style='max-width: 150px;' />" : "") . "
            <h2 style='margin: 10px 0;'>{$this->report_name}</h2>
            <p style='color: #666;'>التاريخ: {$this->report_date} | الوقت: {$this->report_time}</p>
        </div>
        <div style='margin-bottom: 20px;'>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 5px;'><strong>اسم المريض:</strong></td>
                    <td style='padding: 5px;'>{$this->patient_name}</td>
                    <td style='padding: 5px;'><strong>العمر:</strong></td>
                    <td style='padding: 5px;'>{$this->patient_age} سنة</td>
                </tr>
                <tr>
                    <td style='padding: 5px;'><strong>الجنس:</strong></td>
                    <td style='padding: 5px;'>{$this->patient_gender}</td>
                    <td style='padding: 5px;'><strong>تاريخ الزيارة:</strong></td>
                    <td style='padding: 5px;'>{$this->visit_date}</td>
                </tr>
            </table>
        </div>
        <hr style='margin: 20px 0;' />
        ";
    }

    private function generateReportFooter()
    {
        return "
        <hr style='margin: 30px 0 20px;' />
        <div style='margin-top: 30px;'>
            <table style='width: 100%;'>
                <tr>
                    <td style='width: 50%;'>
                        <p><strong>الطبيب المعالج:</strong> {$this->doctor_name}</p>
                        <p><strong>التخصص:</strong> {$this->doctor_specialty}</p>
                        " . ($this->doctor_license ? "<p><strong>رقم الترخيص:</strong> {$this->doctor_license}</p>" : "") . "
                    </td>
                    <td style='width: 50%; text-align: left;'>
                        " . ($this->includeSignature ? "
                        <div style='margin-top: 50px; border-top: 1px solid #000; display: inline-block; padding-top: 5px;'>
                            التوقيع
                        </div>
                        " : "") . "
                    </td>
                </tr>
            </table>
        </div>
        " . ($this->includeWatermark ? "
        <div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); opacity: 0.1; font-size: 100px; color: #000; z-index: -1;'>
            سري
        </div>
        " : "") . "
        ";
    }

    public function loadPreviousReports()
    {
        $query = m_d_medicalreport::with(['doctor', 'template'])
            ->where('id_patient', $this->id_patient);

        // البحث
        if (!empty($this->searchReports)) {
            $query->where(function ($q) {
                $q->where('report_name', 'like', '%' . $this->searchReports . '%')
                  ->orWhere('content', 'like', '%' . $this->searchReports . '%');
            });
        }

        // الفلترة حسب النوع
        if (!empty($this->filterByType)) {
            $query->where('report_type', $this->filterByType);
        }

        // الفلترة حسب الحالة
        if (!empty($this->filterByStatus)) {
            $query->where('status', $this->filterByStatus);
        }

        // الفلترة حسب التاريخ
        if (!empty($this->filterByDate)) {
            $query->whereDate('report_date', $this->filterByDate);
        }

        $this->previousReports = $query->orderByDesc('report_date')->get();
    }

    public function clearReportFilters()
    {
        $this->reset(['searchReports', 'filterByType', 'filterByStatus', 'filterByDate']);
        $this->loadPreviousReports();
    }

    // Updated properties
    public function updatedSearchReports()
    {
        $this->loadPreviousReports();
    }

    public function updatedFilterByType()
    {
        $this->loadPreviousReports();
    }

    public function updatedFilterByStatus()
    {
        $this->loadPreviousReports();
    }

    public function updatedFilterByDate()
    {
        $this->loadPreviousReports();
    }

    public function render()
    {
        return view('livewire.medical-report-form', [
            'reports' => $this->previousReports,
            'reportsCount' => $this->reportsCount,
            'lastReportDate' => $this->lastReportDate,
        ]);
    }
}