<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\hr\hr_offers;
use App\models\hr\hr_candidates;
use App\models\hr\jobs;
use App\models\hr\Employee;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class hrofferscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hr.recruitment.programs.offers.index');
    }

    /**
     * Get offers data for DataTables
     */
    public function getData(Request $request)
    {
        try {
            $offers = hr_offers::with(['candidate', 'job', 'creator']);

            // Apply status filter if provided
            if ($request->has('status') && $request->status != '') {
                $offers = $offers->where('status', $request->status);
            }

            return DataTables::of($offers)
            ->addIndexColumn()
            ->addColumn('candidate_name', function ($offer) {
                return $offer->candidate->full_name ?? '';
            })
            ->addColumn('job_title', function ($offer) {
                return $offer->job->name_ar ?? $offer->job->job_title ?? '';
            })
            ->addColumn('creator_name', function ($offer) {
                return $offer->creator->name ?? '';
            })
            ->addColumn('status_badge', function ($offer) {
                $badgeClass = 'secondary';
                $text = trans('hr.new');
                
                switch ($offer->status) {
                    case 'approved':
                        $badgeClass = 'success';
                        $text = trans('hr.approved');
                        break;
                    case 'rejected':
                        $badgeClass = 'danger';
                        $text = trans('hr.rejected');
                        break;
                }
                
                return '<span class="badge bg-' . $badgeClass . '">' . $text . '</span>';
            })
            ->addColumn('action', function ($offer) {
                $actions = '<div class="d-flex gap-2">';
                $actions .= '<a href="' . route('hr_offers.show', $offer->id) . '" class="btn btn-sm btn-info"><i class="ri-eye-line"></i></a>';
                $actions .= '<a href="' . route('hr_offers.edit', $offer->id) . '" class="btn btn-sm btn-warning"><i class="ri-edit-line"></i></a>';
                
                if ($offer->status === 'new') {
                    $actions .= '<button type="button" class="btn btn-sm btn-success" onclick="approveOffer(' . $offer->id . ')"><i class="ri-check-line"></i></button>';
                    $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="rejectOffer(' . $offer->id . ')"><i class="ri-close-line"></i></button>';
                }
                
                $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteFunction(' . $offer->id . ')"><i class="ri-delete-bin-line"></i></button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
        } catch (\Exception $e) {
            \Log::error('Offers DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while loading the data'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only show accepted candidates who don't already have approved offers
        $candidates = hr_candidates::where('status', 'accepted')
            ->whereDoesntHave('offers', function($query) {
                $query->where('status', 'approved');
            })
            ->with(['requisition.job'])
            ->get();
            
        return view('hr.recruitment.programs.offers.create', compact('candidates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:hr_candidates,id',
            'offer_text' => 'required|string',
            'salary' => 'required|numeric|min:0',
        ]);

        // Get the candidate and auto-fill job_id
        $candidate = hr_candidates::with('requisition')->findOrFail($request->candidate_id);
        
        // Validate that candidate is accepted
        if ($candidate->status !== 'accepted') {
            return redirect()->back()->withErrors(['candidate_id' => __('hr.candidate_must_be_accepted')]);
        }

        // Validate: Cannot create another offer for a candidate who already has an accepted offer
        $existingAcceptedOffer = hr_offers::where('candidate_id', $candidate->id)
                                          ->where('status', 'approved')
                                          ->first();
        if ($existingAcceptedOffer) {
            return redirect()->back()->withErrors(['candidate_id' => __('hr.candidate_already_has_accepted_offer')]);
        }

        $data = $request->all();
        $data['job_id'] = $candidate->requisition->job_id;
        $data['created_by'] = Auth::id();

        hr_offers::create($data);

        return redirect()->route('hr_offers.index')
            ->with('success', __('hr.offer_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $offer = hr_offers::with(['candidate', 'job', 'creator'])->findOrFail($id);
        return view('hr.recruitment.programs.offers.show', compact('offer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $offer = hr_offers::with(['candidate'])->findOrFail($id);
        
        // Show accepted candidates who don't have approved offers (excluding current offer's candidate)
        $candidates = hr_candidates::where('status', 'accepted')
            ->where(function($query) use ($offer) {
                $query->whereDoesntHave('offers', function($subquery) {
                    $subquery->where('status', 'approved');
                })
                ->orWhere('id', $offer->candidate_id); // Include current candidate
            })
            ->with(['requisition.job'])
            ->get();
            
        return view('hr.recruitment.programs.offers.edit', compact('offer', 'candidates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'candidate_id' => 'required|exists:hr_candidates,id',
            'offer_text' => 'required|string',
            'salary' => 'required|numeric|min:0',
        ]);

        $offer = hr_offers::findOrFail($id);
        
        // Get the candidate and auto-fill job_id
        $candidate = hr_candidates::with('requisition')->findOrFail($request->candidate_id);
        
        // Validate that candidate is accepted
        if ($candidate->status !== 'accepted') {
            return redirect()->back()->withErrors(['candidate_id' => __('hr.candidate_must_be_accepted')]);
        }

        // Validate: Cannot create another offer for a candidate who already has an accepted offer
        // (excluding current offer being updated)
        $existingAcceptedOffer = hr_offers::where('candidate_id', $candidate->id)
                                          ->where('status', 'approved')
                                          ->where('id', '!=', $offer->id)
                                          ->first();
        if ($existingAcceptedOffer) {
            return redirect()->back()->withErrors(['candidate_id' => __('hr.candidate_already_has_accepted_offer')]);
        }

        $data = $request->all();
        $data['job_id'] = $candidate->requisition->job_id;

        $offer->update($data);

        return redirect()->route('hr_offers.index')
            ->with('success', __('hr.offer_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $offer = hr_offers::findOrFail($id);
            $offer->delete();

            return response()->json([
                'success' => true,
                'message' => __('hr.offer_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting offer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve an offer
     */
    public function approve($id)
    {
        \Log::info('=== OFFER APPROVAL ROUTE HIT === ID: ' . $id);
        
        try {
            \Log::info('Starting offer approval for ID: ' . $id);
            
            $offer = hr_offers::with(['candidate.requisition', 'job'])->findOrFail($id);
            \Log::info('Offer found: ' . $offer->id . ', Status: ' . $offer->status);
            
            // Validate: Cannot approve if candidate already has an accepted offer
            $existingAcceptedOffer = hr_offers::where('candidate_id', $offer->candidate_id)
                                              ->where('status', 'approved')
                                              ->where('id', '!=', $offer->id)
                                              ->first();
            if ($existingAcceptedOffer) {
                \Log::warning('Candidate already has accepted offer: ' . $existingAcceptedOffer->id);
                return response()->json([
                    'success' => false,
                    'message' => __('hr.candidate_already_has_accepted_offer')
                ], 400);
            }

            // Validate: Cannot accept more offers than required candidates in requisition
            $requisition = $offer->candidate->requisition;
            $currentAcceptedOffers = hr_offers::whereHas('candidate', function($query) use ($requisition) {
                $query->where('requisition_id', $requisition->id);
            })->where('status', 'approved')->count();

            if ($currentAcceptedOffers >= $requisition->number_of_candidates) {
                \Log::warning('Requisition already has maximum accepted offers: ' . $currentAcceptedOffers . '/' . $requisition->number_of_candidates);
                return response()->json([
                    'success' => false,
                    'message' => __('hr.requisition_maximum_offers_reached', [
                        'current' => $currentAcceptedOffers,
                        'max' => $requisition->number_of_candidates
                    ])
                ], 400);
            }

            $offer->update(['status' => 'approved']);
            \Log::info('Offer status updated to approved');

            // Auto-create employee when offer is approved
            \Log::info('Calling createEmployeeFromOffer...');
            $this->createEmployeeFromOffer($offer);
            \Log::info('createEmployeeFromOffer completed');

            return response()->json([
                'success' => true,
                'message' => __('hr.offer_approved_successfully')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving offer: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error approving offer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject an offer
     */
    public function reject($id)
    {
        try {
            $offer = hr_offers::findOrFail($id);
            $offer->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => __('hr.offer_rejected_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting offer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-create employee when offer is approved
     */
    private function createEmployeeFromOffer($offer)
    {
        try {
            \Log::info('=== Starting createEmployeeFromOffer ===');
            $candidate = $offer->candidate;
            \Log::info('Candidate data: ' . json_encode($candidate->toArray()));
            
            // Check if employee already exists for this candidate
            $existingEmployee = Employee::where('identification_number', $candidate->national_id)->first();
            if ($existingEmployee) {
                \Log::info('Employee already exists for candidate: ' . $candidate->national_id);
                return; // Employee already exists
            }

            // Create new employee with correct field mapping
            $employeeData = [
                'full_name' => $candidate->full_name,
                'first_name_ar' => $candidate->first_name,
                'second_name_ar' => $candidate->second_name,
                'third_name_ar' => $candidate->third_name,
                'fourth_name_ar' => $candidate->fourth_name,
                'date_of_birth' => $candidate->birth_date ? $candidate->birth_date->format('Y-m-d') : null,
                'identification_number' => $candidate->national_id,
                'email' => $candidate->email,
                'city' => $candidate->city,
                'nationality' => $candidate->nationality,
                'job_position' => $offer->job_id,
                'employee_status' => 'in_service',
                'employee_type' => 'admin', // Required enum: physician, nursing, admin
                'has_valid_contract' => 1, // Required boolean field
                'hiring_date' => now()->format('Y-m-d'),
                'contract_open_date' => now()->format('Y-m-d'),
            ];

            \Log::info('Employee data to create: ' . json_encode($employeeData));
            
            $employee = Employee::create($employeeData);
            
            \Log::info('Employee created successfully with ID: ' . $employee->id);
            \Log::info('Employee created: ' . json_encode($employee->toArray()));
            
        } catch (\Exception $e) {
            \Log::error('Error creating employee from offer: ' . $e->getMessage());
            \Log::error('SQL Error Info: ' . json_encode($e->errorInfo ?? 'No SQL error info'));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            // Don't throw exception to avoid breaking the offer approval
        }
    }
}
