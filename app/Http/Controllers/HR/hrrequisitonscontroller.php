<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\hr\hr_requisitions;
use App\models\hr\jobs;
use App\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class hrrequisitonscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hr.recruitment.programs.requisitions.index');
    }

    /**
     * Get requisitions data for DataTables
     */
    public function getData()
    {
        try {
            $requisitions = hr_requisitions::with(['creator', 'approver', 'candidates', 'job']);

            return DataTables::of($requisitions)
            ->addColumn('requisition_name', function ($requisition) {
                return $requisition->requisition_name;
            })
            ->addColumn('job_title', function ($requisition) {
                return $requisition->job->name_en ?? '';
            })
            ->addColumn('creator_name', function ($requisition) {
                return $requisition->creator->name ?? '';
            })
            ->addColumn('approver_name', function ($requisition) {
                return $requisition->approver->name ?? '';
            })
            ->addColumn('candidates_count', function ($requisition) {
                return $requisition->candidates->count() . '/' . $requisition->number_of_candidates;
            })
            ->addColumn('status_badge', function ($requisition) {
                $badgeClass = 'secondary';
                $text = trans('hr.pending');
                
                switch ($requisition->status) {
                    case 'approved':
                        $badgeClass = 'success';
                        $text = trans('hr.approved');
                        break;
                    case 'refused':
                        $badgeClass = 'danger';
                        $text = trans('hr.refused');
                        break;
                }
                
                return '<span class="badge bg-' . $badgeClass . '">' . $text . '</span>';
            })
            ->addColumn('action', function ($requisition) {
                $actions = '<div class="d-flex gap-2">';
                $actions .= '<a href="' . route('hr_requisitions.show', $requisition->id) . '" class="btn btn-sm btn-info"><i class="ri-eye-line"></i></a>';
                $actions .= '<a href="' . route('hr_requisitions.edit', $requisition->id) . '" class="btn btn-sm btn-warning"><i class="ri-edit-line"></i></a>';
                
                if ($requisition->status === 'pending') {
                    $actions .= '<button type="button" class="btn btn-sm btn-success" onclick="approveFunction(' . $requisition->id . ')"><i class="ri-check-line"></i></button>';
                    $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="refuseFunction(' . $requisition->id . ')"><i class="ri-close-line"></i></button>';
                }
                
                $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteFunction(' . $requisition->id . ')"><i class="ri-delete-bin-line"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
        } catch (\Exception $e) {
            \Log::error('Requisitions DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while loading the data'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jobs = jobs::select('id', 'name_en', 'name_ar')->get();
        return view('hr.recruitment.programs.requisitions.create', compact('jobs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'requisition_name' => 'required|string|max:255',
            'job_id' => 'required|exists:hr_jobs,id',
            'number_of_candidates' => 'required|integer|min:1',
            'required_experience_years' => 'nullable|integer|min:0',
            'specifications' => 'nullable|string',
        ]);

        hr_requisitions::create([
            'requisition_name' => $request->requisition_name,
            'job_id' => $request->job_id,
            'number_of_candidates' => $request->number_of_candidates,
            'required_experience_years' => $request->required_experience_years,
            'specifications' => $request->specifications,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('hr_requisitions.index')
            ->with('success', __('hr.requisition_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $requisition = hr_requisitions::with(['creator', 'approver', 'candidates', 'job'])->findOrFail($id);
        return view('hr.recruitment.programs.requisitions.show', compact('requisition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $requisition = hr_requisitions::findOrFail($id);
        $jobs = jobs::select('id', 'name_en', 'name_ar')->get();
        return view('hr.recruitment.programs.requisitions.edit', compact('requisition', 'jobs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'requisition_name' => 'required|string|max:255',
            'job_id' => 'required|exists:hr_jobs,id',
            'number_of_candidates' => 'required|integer|min:1',
            'required_experience_years' => 'nullable|integer|min:0',
            'specifications' => 'nullable|string',
        ]);

        $requisition = hr_requisitions::findOrFail($id);
        
        $requisition->update([
            'requisition_name' => $request->requisition_name,
            'job_id' => $request->job_id,
            'number_of_candidates' => $request->number_of_candidates,
            'required_experience_years' => $request->required_experience_years,
            'specifications' => $request->specifications,
        ]);

        return redirect()->route('hr_requisitions.index')
            ->with('success', __('hr.requisition_updated_successfully'));
    }

    /**
     * Approve a requisition.
     */
    public function approve($id)
    {
        try {
            $requisition = hr_requisitions::findOrFail($id);
            $requisition->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('hr.requisition_approved_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('Requisition Approval Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error approving requisition: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuse a requisition.
     */
    public function refuse($id)
    {
        try {
            $requisition = hr_requisitions::findOrFail($id);
            $requisition->update([
                'status' => 'refused',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('hr.requisition_refused_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('Requisition Refusal Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error refusing requisition: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $requisition = hr_requisitions::findOrFail($id);
            $requisition->delete();

            return response()->json([
                'success' => true,
                'message' => __('hr.requisition_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('Requisition Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting requisition: ' . $e->getMessage()
            ], 500);
        }
    }
}
