<?php

namespace App\Http\Controllers\hr;

use App\models\hr\Employee;
use Illuminate\Http\Request;
use App\Traits\web\file_storage;
use App\models\general\countries;
use App\models\general\city;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\models\hr\Department;
use App\models\hr\section;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\models\hr\jobs;

class EmployeeController extends Controller
{
    use file_storage;

    /**
     * Display a listing of the employees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hr.employees.programs.employees.index');
    }

    public function get_employees()
    {
        $employees = Employee::with('emp_dep', 'emp_sec', 'emp_pos', 'schedule');

        return DataTables::of($employees)

            ->addColumn('employee_status', function ($employees) {
                // تحديد الحالة باللغة العربية
                $employee_status = $employees->employee_status;
                switch ($employee_status) {
                    case 'in_service':
                        return '<span class="badge bg-success">' . trans('hr.in_service') . '</span>';
                    case 'out_of_service':
                        return '<span class="badge bg-warning">' . trans('hr.out_of_service') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })

            ->addColumn('has_valid_contract', function ($employees) {
                // تحديد الحالة باللغة العربية
                $has_valid_contract = $employees->has_valid_contract;
                switch ($has_valid_contract) {
                    case true:
                        return '<span class="badge bg-success">' . trans('hr.yes') . '</span>';
                    case false:
                        return '<span class="badge bg-warning">' . trans('hr.no') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })

            ->addColumn('schedule', function ($employees) {
                if ($employees->schedule) {
                    return $employees->schedule->name . ' (' . number_format($employees->schedule->total_hours ?? 0, 2) . ' hrs)';
                }
                return '-';
            })

            ->editColumn('created_at', function ($employees) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($employees->created_at)->format('d/m/Y h:i A');
            })

            ->addColumn('emp_dep', function ($employees) {
                return optional($employees->emp_dep)->name_ar ?: '-';
            })

            ->addColumn('emp_pos', function ($employees) {
                return optional($employees->emp_pos)->name_ar ?: '-';
            })

            ->addColumn('action', function ($employees) {
                $encryptedId = Crypt::encryptString($employees->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $employees->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('employees.edit', ['id' => $employees->id]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['emp_dep', 'department', 'emp_pos', 'has_valid_contract', 'employee_status', 'schedule', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $departments = Department::all();
        $sections = section::all();
        $jobs = jobs::all();
        $schedules = \App\models\hr\hr_schedules::where('is_active', true)->get();
        $cities = city::all();
        return view('hr.employees.programs.employees.create', compact('country', 'departments', 'sections', 'jobs', 'schedules', 'cities'));
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'full_name' => 'required|string|max:255',
            'employee_code' => 'nullable|unique:employees,employee_code',
            'gahar_code' => 'nullable',
            'employee_type' => 'required',
            'hiring_date' => 'required',
        ]);
        // Handle file uploads
        $cvPath = $request->file('cv') ? $this->file_storage($request->file('cv'), 'employee_documents') : null;
        $militaryPath = $request->file('militery') ? $this->file_storage($request->file('militery'), 'employee_documents') : null;
        $graduationPath = $request->file('graduation_certificate') ? $this->file_storage($request->file('graduation_certificate'), 'employee_documents') : null;
        $birthCertificatePath = $request->file('birth_certificate') ? $this->file_storage($request->file('birth_certificate'), 'employee_documents') : null;
        $photoPath = $request->file('employee_photo') ? $this->file_storage($request->file('employee_photo'), 'employee_photos') : null;

        $idFrontPath = $request->file('id_front') ? $this->file_storage($request->file('id_front'), 'employee_documents') : null;
        $idBackPath = $request->file('id_back') ? $this->file_storage($request->file('id_back'), 'employee_documents') : null;
        $criminalityPath = $request->file('criminality') ? $this->file_storage($request->file('criminality'), 'employee_documents') : null;
        $insurancePrintPath = $request->file('insurance_print') ? $this->file_storage($request->file('insurance_print'), 'employee_documents') : null;
        $kaabPath = $request->file('kaab') ? $this->file_storage($request->file('kaab'), 'employee_documents') : null;
        $workPermitPath = $request->file('work_permit') ? $this->file_storage($request->file('work_permit'), 'employee_documents') : null;
        $unionCardPath = $request->file('union_card') ? $this->file_storage($request->file('union_card'), 'employee_documents') : null;
        $unionDescPath = $request->file('union_desc') ? $this->file_storage($request->file('union_desc'), 'employee_documents') : null;
        $form111Path = $request->file('111_form') ? $this->file_storage($request->file('111_form'), 'employee_documents') : null;
        $fullDocumentPath = $request->file('full_document') ? $this->file_storage($request->file('full_document'), 'employee_documents') : null;
        $contractDocumentPath = $request->file('contract_document') ? $this->file_storage($request->file('contract_document'), 'employee_documents') : null;
        $declarationsPath = $request->file('declarations') ? $this->file_storage($request->file('declarations'), 'employee_documents') : null;
        $genSpecPrepPath = $request->file('gen_spec_prep') ? $this->file_storage($request->file('gen_spec_prep'), 'employee_documents') : null;
        $privilegesPath = $request->file('privileges') ? $this->file_storage($request->file('privileges'), 'employee_documents') : null;
        $vaccinationsPath = $request->file('vaccinations') ? $this->file_storage($request->file('vaccinations'), 'employee_documents') : null;
        $certificatesPath = $request->file('certificates') ? $this->file_storage($request->file('certificates'), 'employee_documents') : null;
        $internshipCertificatePath = $request->file('internship_certificate') ? $this->file_storage($request->file('internship_certificate'), 'employee_documents') : null;
        $experienceCertificatePath = $request->file('experience_certificate') ? $this->file_storage($request->file('experience_certificate'), 'employee_documents') : null;
        $personalPhotoPath = $request->file('personal_photo') ? $this->file_storage($request->file('personal_photo'), 'employee_photos') : null;
        $otherDocumentsPath = $request->file('other_documents') ? $this->file_storage($request->file('other_documents'), 'employee_documents') : null;

        // Create the employee record
        $date_of_birth = $request->input('date_of_birth');
        $gender = $request->input('gender');
        $identification_number = $request->input('identification_number');
        if ($identification_number && strlen($identification_number) === 14) {
            // Egyptian ID logic
            $century = substr($identification_number, 0, 1);
            $year = substr($identification_number, 1, 2);
            $month = substr($identification_number, 3, 2);
            $day = substr($identification_number, 5, 2);
            $gender_digit = substr($identification_number, 12, 1);
            if ($century == '2') {
                $year = '19' . $year;
            } elseif ($century == '3') {
                $year = '20' . $year;
            }
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                $date_of_birth = $year . '-' . $month . '-' . $day;
            }
            $gender = ((int)$gender_digit % 2 === 0) ? trans('hr.Female') : trans('hr.Male');
        }
        $employee = Employee::create([
            'full_name' => $request->input('full_name'),
            'first_name_ar' => $request->input('first_name_ar'),
            'second_name_ar' => $request->input('second_name_ar'),
            'third_name_ar' => $request->input('third_name_ar'),
            'fourth_name_ar' => $request->input('fourth_name_ar'),
            'first_name_en' => $request->input('first_name_en'),
            'second_name_en' => $request->input('second_name_en'),
            'third_name_en' => $request->input('third_name_en'),
            'fourth_name_en' => $request->input('fourth_name_en'),
            'employee_code' => $request->input('employee_code'),
            'gahar_code' => $request->input('gahar_code'),
            'employee_status' => $request->input('employee_status'),
            'marital_status' => $request->input('marital_status'),
            'identification_number' => $identification_number,
            'passport_number' => $request->input('passport_number'),
            'religion' => $request->input('religion'),
            'nationality' => $request->input('nationality'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'district' => $request->input('district'),
            'full_address' => $request->input('full_address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'landline' => $request->input('landline'),
            'fax' => $request->input('fax'),
            'emergency_name' => $request->input('emergency_name'),
            'emergency_number' => $request->input('emergency_number'),
            'relationship' => $request->input('relationship'),
            'department' => $request->input('department'),
            'section' => $request->input('section'),
            'job_position' => $request->input('job_position'),
            'contract_open_date' => $request->input('contract_open_date'),
            'hiring_date' => $request->input('hiring_date'),
            'contract_end_date' => $request->input('contract_end_date'),
            'employee_type' => $request->input('employee_type'),
            'schedule_id' => $request->input('schedule_id'),
            'cv' => $cvPath,
            'militery' => $militaryPath,
            'graduation_certificate' => $graduationPath,
            'birth_certificate' => $birthCertificatePath,
            'employee_photo' => $photoPath,
            'id_front' => $idFrontPath,
            'id_back' => $idBackPath,
            'criminality' => $criminalityPath,
            'insurance_print' => $insurancePrintPath,
            'kaab' => $kaabPath,
            'work_permit' => $workPermitPath,
            'union_card' => $unionCardPath,
            'union_desc' => $unionDescPath,
            '111_form' => $form111Path,
            'full_document' => $fullDocumentPath,
            'contract_document' => $contractDocumentPath,
            'declarations' => $declarationsPath,
            'gen_spec_prep' => $genSpecPrepPath,
            'privileges' => $privilegesPath,
            'vaccinations' => $vaccinationsPath,
            'certificates' => $certificatesPath,
            'internship_certificate' => $internshipCertificatePath,
            'experience_certificate' => $experienceCertificatePath,
            'personal_photo' => $personalPhotoPath,
            'other_documents' => $otherDocumentsPath,
            'date_of_birth' => $date_of_birth,
            'gender' => $gender,
        ]);

        // Save certificates
        if ($request->has('certificates_json')) {
            $certificates = json_decode($request->input('certificates_json'), true);
            if (is_array($certificates)) {
                foreach ($certificates as $index => $cert) {
                    $fileField = 'certificate_file_' . $index;
                    $certificateFilePath = null;
                    if ($request->hasFile($fileField)) {
                        $certificateFilePath = $this->file_storage($request->file($fileField), 'employee_certificates');
                    }
                    \App\models\HR\hr_certificates::create([
                        'employee_id' => $employee->id,
                        'certificate_name' => $cert['certificate_name'] ?? '',
                        'certificate_type_id' => isset($cert['certificate_type_id']) && $cert['certificate_type_id'] !== '' ? (int)$cert['certificate_type_id'] : null,
                        'graduation_date' => $cert['graduation_date'] ?? null,
                        'university_id' => isset($cert['university_id']) && $cert['university_id'] !== '' ? (int)$cert['university_id'] : null,
                        'certificate_file' => $certificateFilePath ?? '',
                    ]);
                }
            }
        }

        // Redirect with success message
        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $country = countries::where('account_id', Auth::user()->id_account)->get();
        $departments = Department::all();
        $sections = section::all();
        $jobs = jobs::all();
        $schedules = \App\models\hr\hr_schedules::where('is_active', true)->get();
        $certificates = \App\models\HR\hr_certificates::where('employee_id', $employee->id)->get();
        return view('hr.employees.programs.employees.edit', compact('employee', 'country', 'departments', 'sections', 'jobs', 'schedules', 'certificates'));
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        // Validate required fields
        $request->validate([
            'full_name' => 'required|string|max:255',
            'employee_code' => 'required|unique:employees,employee_code,' . $id,
            'gahar_code' => 'required',
            'hiring_date' => 'required',
        ]);

        // Handle file uploads, retaining old files if no new ones are uploaded
        $cvPath = $request->file('cv') ? $this->file_storage($request->file('cv'), 'employee_documents') : $employee->cv;
        $militaryPath = $request->file('militery') ? $this->file_storage($request->file('militery'), 'employee_documents') : $employee->militery;
        $graduationPath = $request->file('graduation_certificate') ? $this->file_storage($request->file('graduation_certificate'), 'employee_documents') : $employee->graduation_certificate;
        $birthCertificatePath = $request->file('birth_certificate') ? $this->file_storage($request->file('birth_certificate'), 'employee_documents') : $employee->birth_certificate;
        $photoPath = $request->file('employee_photo') ? $this->file_storage($request->file('employee_photo'), 'employee_photos') : $employee->employee_photo;

        $idFrontPath = $request->file('id_front') ? $this->file_storage($request->file('id_front'), 'employee_documents') : $employee->id_front;
        $idBackPath = $request->file('id_back') ? $this->file_storage($request->file('id_back'), 'employee_documents') : $employee->id_back;
        $criminalityPath = $request->file('criminality') ? $this->file_storage($request->file('criminality'), 'employee_documents') : $employee->criminality;
        $insurancePrintPath = $request->file('insurance_print') ? $this->file_storage($request->file('insurance_print'), 'employee_documents') : $employee->insurance_print;
        $kaabPath = $request->file('kaab') ? $this->file_storage($request->file('kaab'), 'employee_documents') : $employee->kaab;
        $workPermitPath = $request->file('work_permit') ? $this->file_storage($request->file('work_permit'), 'employee_documents') : $employee->work_permit;
        $unionCardPath = $request->file('union_card') ? $this->file_storage($request->file('union_card'), 'employee_documents') : $employee->union_card;
        $unionDescPath = $request->file('union_desc') ? $this->file_storage($request->file('union_desc'), 'employee_documents') : $employee->union_desc;
        $form111Path = $request->file('111_form') ? $this->file_storage($request->file('111_form'), 'employee_documents') : $employee->form111;
        $fullDocumentPath = $request->file('full_document') ? $this->file_storage($request->file('full_document'), 'employee_documents') : $employee->full_document;
        $contractDocumentPath = $request->file('contract_document') ? $this->file_storage($request->file('contract_document'), 'employee_documents') : $employee->contract_document;
        $declarationsPath = $request->file('declarations') ? $this->file_storage($request->file('declarations'), 'employee_documents') : $employee->declarations;
        $genSpecPrepPath = $request->file('gen_spec_prep') ? $this->file_storage($request->file('gen_spec_prep'), 'employee_documents') : $employee->gen_spec_prep;
        $privilegesPath = $request->file('privileges') ? $this->file_storage($request->file('privileges'), 'employee_documents') : $employee->privileges;
        $vaccinationsPath = $request->file('vaccinations') ? $this->file_storage($request->file('vaccinations'), 'employee_documents') : $employee->vaccinations;
        $certificatesPath = $request->file('certificates') ? $this->file_storage($request->file('certificates'), 'employee_documents') : $employee->certificates;
        $internshipCertificatePath = $request->file('internship_certificate') ? $this->file_storage($request->file('internship_certificate'), 'employee_documents') : $employee->internship_certificate;
        $experienceCertificatePath = $request->file('experience_certificate') ? $this->file_storage($request->file('experience_certificate'), 'employee_documents') : $employee->experience_certificate;
        $personalPhotoPath = $request->file('personal_photo') ? $this->file_storage($request->file('personal_photo'), 'employee_photos') : $employee->personal_photo;
        $otherDocumentsPath = $request->file('other_documents') ? $this->file_storage($request->file('other_documents'), 'employee_documents') : $employee->other_documents;

        // Update employee record
        $date_of_birth = $request->input('date_of_birth');
        $gender = $request->input('gender');
        $identification_number = $request->input('identification_number');
        if ($identification_number && strlen($identification_number) === 14) {
            // Egyptian ID logic
            $century = substr($identification_number, 0, 1);
            $year = substr($identification_number, 1, 2);
            $month = substr($identification_number, 3, 2);
            $day = substr($identification_number, 5, 2);
            $gender_digit = substr($identification_number, 12, 1);
            if ($century == '2') {
                $year = '19' . $year;
            } elseif ($century == '3') {
                $year = '20' . $year;
            }
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                $date_of_birth = $year . '-' . $month . '-' . $day;
            }
            $gender = ((int)$gender_digit % 2 === 0) ? trans('hr.Female') : trans('hr.Male');
        }
        $employee->update([
            'full_name' => $request->input('full_name'),
            'first_name_ar' => $request->input('first_name_ar'),
            'second_name_ar' => $request->input('second_name_ar'),
            'third_name_ar' => $request->input('third_name_ar'),
            'fourth_name_ar' => $request->input('fourth_name_ar'),
            'first_name_en' => $request->input('first_name_en'),
            'second_name_en' => $request->input('second_name_en'),
            'third_name_en' => $request->input('third_name_en'),
            'fourth_name_en' => $request->input('fourth_name_en'),
            'employee_code' => $request->input('employee_code'),
            'gahar_code' => $request->input('gahar_code'),
            'employee_status' => $request->input('employee_status'),
            'marital_status' => $request->input('marital_status'),
            'identification_number' => $identification_number,
            'passport_number' => $request->input('passport_number'),
            'religion' => $request->input('religion'),
            'nationality' => $request->input('nationality'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'district' => $request->input('district'),
            'full_address' => $request->input('full_address'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'landline' => $request->input('landline'),
            'fax' => $request->input('fax'),
            'emergency_name' => $request->input('emergency_name'),
            'emergency_number' => $request->input('emergency_number'),
            'relationship' => $request->input('relationship'),
            'department' => $request->input('department'),
            'section' => $request->input('section'),
            'job_position' => $request->input('job_position'),
            'contract_open_date' => $request->input('contract_open_date'),
            'hiring_date' => $request->input('hiring_date'),
            'contract_end_date' => $request->input('contract_end_date'),
            'employee_type' => $request->input('employee_type'),
            'schedule_id' => $request->input('schedule_id'),
            'cv' => $cvPath,
            'militery' => $militaryPath,
            'graduation_certificate' => $graduationPath,
            'birth_certificate' => $birthCertificatePath,
            'employee_photo' => $photoPath,
            'id_front' => $idFrontPath,
            'id_back' => $idBackPath,
            'criminality' => $criminalityPath,
            'insurance_print' => $insurancePrintPath,
            'kaab' => $kaabPath,
            'work_permit' => $workPermitPath,
            'union_card' => $unionCardPath,
            'union_desc' => $unionDescPath,
            '111_form' => $form111Path,
            'full_document' => $fullDocumentPath,
            'contract_document' => $contractDocumentPath,
            'declarations' => $declarationsPath,
            'gen_spec_prep' => $genSpecPrepPath,
            'privileges' => $privilegesPath,
            'vaccinations' => $vaccinationsPath,
            'certificates' => $certificatesPath,
            'internship_certificate' => $internshipCertificatePath,
            'experience_certificate' => $experienceCertificatePath,
            'personal_photo' => $personalPhotoPath,
            'other_documents' => $otherDocumentsPath,
            'date_of_birth' => $date_of_birth,
            'gender' => $gender,
        ]);

        // Update certificates
        \App\models\HR\hr_certificates::where('employee_id', $employee->id)->delete();
        if ($request->has('certificates_json')) {
            $certificates = json_decode($request->input('certificates_json'), true);
            if (is_array($certificates)) {
                foreach ($certificates as $index => $cert) {
                    $fileField = 'certificate_file_' . $index;
                    $certificateFilePath = null;
                    if ($request->hasFile($fileField)) {
                        $certificateFilePath = $this->file_storage($request->file($fileField), 'employee_certificates');
                    }
                    \App\models\HR\hr_certificates::create([
                        'employee_id' => $employee->id,
                        'certificate_name' => $cert['certificate_name'] ?? '',
                        'certificate_type_id' => isset($cert['certificate_type_id']) && $cert['certificate_type_id'] !== '' ? (int)$cert['certificate_type_id'] : null,
                        'graduation_date' => $cert['graduation_date'] ?? null,
                        'university_id' => isset($cert['university_id']) && $cert['university_id'] !== '' ? (int)$cert['university_id'] : null,
                        'certificate_file' => $certificateFilePath ?? '',
                    ]);
                }
            }
        }

        // Redirect with success message
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }
}