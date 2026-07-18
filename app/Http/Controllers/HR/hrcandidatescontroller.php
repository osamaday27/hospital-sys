<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\hr\hr_candidates;
use App\models\hr\hr_requisitions;
use App\models\general\city;
use App\models\general\nationalities;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\web\file_storage;

class hrcandidatescontroller extends Controller
{
    use file_storage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requisitions = hr_requisitions::approved()->with(['job', 'candidates'])->get();
        return view('hr.recruitment.programs.candidates.index', compact('requisitions'));
    }

    /**
     * Get candidates data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $candidates = hr_candidates::with(['requisition', 'getcity', 'getnationality']);

            // Filter by requisition if provided
            if ($request->has('requisition_id') && $request->requisition_id) {
                $candidates->where('requisition_id', $request->requisition_id);
            }

            return DataTables::of($candidates)
            ->addColumn('full_name', function ($candidate) {
                return $candidate->full_name;
            })
            ->addColumn('requisition_name', function ($candidate) {
                return $candidate->requisition->requisition_name ?? '';
            })
            ->addColumn('city_name', function ($candidate) {
                return $candidate->getcity->name ?? '';
            })
            ->addColumn('nationality_name', function ($candidate) {
                return $candidate->getnationality->name ?? '';
            })
            ->addColumn('age', function ($candidate) {
                return $candidate->age;
            })
            ->addColumn('gender_text', function ($candidate) {
                return $candidate->gender === 'male' ? trans('hr.male') : trans('hr.female');
            })
            ->addColumn('status_badge', function ($candidate) {
                return '<span class="badge bg-' . $candidate->status_badge . '">' . $candidate->status_text . '</span>';
            })
            ->addColumn('cv_file', function ($candidate) {
                if ($candidate->cv_file) {
                    return '<a href="' . asset('attachments/' . ltrim($candidate->cv_file, '/')) . '" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ri-download-line"></i> ' . trans('hr.download') . '
                            </a>';
                }
                return '<span class="text-muted">' . trans('hr.no_cv') . '</span>';
            })
            ->addColumn('action', function ($candidate) {
                $actions = '<div class="d-flex gap-2">';
                $actions .= '<a href="' . route('hr_candidates.show', $candidate->id) . '" class="btn btn-sm btn-info"><i class="ri-eye-line"></i></a>';
                $actions .= '<a href="' . route('hr_candidates.edit', $candidate->id) . '" class="btn btn-sm btn-warning"><i class="ri-edit-line"></i></a>';
                
                // Evaluation workflow buttons
                if ($candidate->status === 'new') {
                    $actions .= '<button type="button" class="btn btn-sm btn-primary" onclick="updateStatus(' . $candidate->id . ', \'shortlisted\')" title="' . trans('hr.shortlist') . '"><i class="ri-list-check"></i></button>';
                } elseif ($candidate->status === 'shortlisted') {
                    $actions .= '<button type="button" class="btn btn-sm btn-warning" onclick="updateStatus(' . $candidate->id . ', \'accept_hr_interview\')" title="' . trans('hr.hr_interview') . '"><i class="ri-user-voice-line"></i></button>';
                } elseif ($candidate->status === 'accept_hr_interview') {
                    $actions .= '<button type="button" class="btn btn-sm btn-info" onclick="updateStatus(' . $candidate->id . ', \'accept_technical_interview\')" title="' . trans('hr.technical_interview') . '"><i class="ri-code-line"></i></button>';
                } elseif ($candidate->status === 'accept_technical_interview') {
                    $actions .= '<button type="button" class="btn btn-sm btn-success" onclick="updateStatus(' . $candidate->id . ', \'accepted\')" title="' . trans('hr.accept_candidate') . '"><i class="ri-check-line"></i></button>';
                }
                
                // Refuse button (available for all except refused)
                if ($candidate->status !== 'refused' && $candidate->status !== 'accepted') {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="updateStatus(' . $candidate->id . ', \'refused\')" title="' . trans('hr.refuse_candidate') . '"><i class="ri-close-line"></i></button>';
                }
                
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFunction(' . $candidate->id . ')"><i class="ri-delete-bin-line"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action', 'cv_file', 'status_badge'])
            ->make(true);
        } catch (\Exception $e) {
            \Log::error('Candidates DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while loading the data'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $requisitions = hr_requisitions::approved()->get();
        $cities = city::orderBy('name->ar')->get();
        $nationalities = nationalities::orderBy('name->ar')->get();
        return view('hr.recruitment.programs.candidates.create', compact('requisitions', 'cities', 'nationalities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'requisition_id' => 'required|exists:hr_requisitions,id',
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'third_name' => 'required|string|max:255',
            'fourth_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'national_id' => 'required|string|max:255|unique:hr_candidates,national_id',
            'email' => 'required|email|max:255|unique:hr_candidates,email',
            'city' => 'required|exists:city,id',
            'nationality' => 'required|exists:nationalities_settings,id',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        // Validate that the requisition is approved and not completed
        $requisition = hr_requisitions::findOrFail($request->requisition_id);
        if (!$requisition->isApproved()) {
            return redirect()->back()->withErrors(['requisition_id' => __('hr.requisition_must_be_approved')]);
        }
        
        if ($requisition->isCompleted()) {
            return redirect()->back()->withErrors(['requisition_id' => __('hr.requisition_is_completed')]);
        }

        $data = $request->all();
        
        // Handle CV file upload using the same method as employees
        if ($request->hasFile('cv_file')) {
            $cvPaths = $this->file_storage($request->file('cv_file'), 'candidates_cv');
            $data['cv_file'] = $cvPaths[0]; // Get the first (and only) path
        }

        hr_candidates::create($data);

        return redirect()->route('hr_candidates.index')
            ->with('success', __('hr.candidate_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $candidate = hr_candidates::with(['requisition'])->findOrFail($id);
        return view('hr.recruitment.programs.candidates.show', compact('candidate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $candidate = hr_candidates::findOrFail($id);
        $requisitions = hr_requisitions::approved()->get();
        $cities = city::orderBy('name->ar')->get();
        $nationalities = nationalities::orderBy('name->ar')->get();
        return view('hr.recruitment.programs.candidates.edit', compact('candidate', 'requisitions', 'cities', 'nationalities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'requisition_id' => 'required|exists:hr_requisitions,id',
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'third_name' => 'required|string|max:255',
            'fourth_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'national_id' => 'required|string|max:255|unique:hr_candidates,national_id,' . $id,
            'email' => 'required|email|max:255|unique:hr_candidates,email,' . $id,
            'city' => 'required|exists:city,id',
            'nationality' => 'required|exists:nationalities_settings,id',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        $candidate = hr_candidates::findOrFail($id);
        $data = $request->except('cv_file');
        
        // Handle CV file upload using the same method as employees
        if ($request->hasFile('cv_file')) {
            $cvPaths = $this->file_storage($request->file('cv_file'), 'candidates_cv');
            $data['cv_file'] = $cvPaths[0]; // Get the first (and only) path
        }

        $candidate->update($data);

        return redirect()->route('hr_candidates.index')
            ->with('success', __('hr.candidate_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $candidate = hr_candidates::findOrFail($id);
            $candidate->delete();

            return response()->json([
                'success' => true,
                'message' => __('hr.candidate_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('Candidate Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting candidate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update candidate status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:new,accepted,refused,shortlisted,accept_hr_interview,accept_technical_interview'
            ]);

            $candidate = hr_candidates::with('requisition')->findOrFail($id);
            $newStatus = $request->status;

            // Validate workflow transitions
            $allowedTransitions = [
                'new' => ['shortlisted', 'refused'],
                'shortlisted' => ['accept_hr_interview', 'refused'],
                'accept_hr_interview' => ['accept_technical_interview', 'refused'],
                'accept_technical_interview' => ['accepted', 'refused'],
                'refused' => [], // No transitions from refused
                'accepted' => [], // No transitions from accepted
            ];

            if (!in_array($newStatus, $allowedTransitions[$candidate->status] ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => __('hr.invalid_status_transition')
                ], 400);
            }

            // If accepting candidate, check if requisition is completed
            if ($newStatus === 'accepted') {
                $acceptedCount = $candidate->requisition->candidates()->where('status', 'accepted')->count();
                if ($acceptedCount >= $candidate->requisition->number_of_candidates) {
                    return response()->json([
                        'success' => false,
                        'message' => __('hr.requisition_already_completed')
                    ], 400);
                }
            }

            $candidate->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => __('hr.candidate_status_updated_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

}
