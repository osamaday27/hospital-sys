<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\models\hr\hr_appraisal_conf;
use App\models\hr\hr_appraisal_terms_conf;
use App\models\hr\jobs;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class hrappraisalconfcontroller extends Controller
{
    public function index()
    {
        return view('hr.appraisal.settings.appraisal_conf.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $jobs = jobs::all();
        return view('hr.appraisal.settings.appraisal_conf.create', compact('jobs'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'job_id' => 'required|exists:hr_jobs,id',
            'terms.KPIs' => 'array',
            'terms.discipline' => 'array',
            'terms.efficiency' => 'array',
            'terms.KPIs.*' => 'nullable|string|max:255',
            'terms.discipline.*' => 'nullable|string|max:255',
            'terms.efficiency.*' => 'nullable|string|max:255',
            'percentages.KPIs.*' => 'nullable|numeric|min:0|max:100',
            'percentages.discipline.*' => 'nullable|numeric|min:0|max:100',
            'percentages.efficiency.*' => 'nullable|numeric|min:0|max:100',
        ]);

        // Check if at least one term is provided
        $hasTerms = false;
        $totalPercentage = 0;
        
        foreach (['KPIs', 'discipline', 'efficiency'] as $category) {
            if (!empty($request->input("terms.{$category}"))) {
                $terms = $request->input("terms.{$category}", []);
                $percentages = $request->input("percentages.{$category}", []);
                
                foreach ($terms as $index => $termName) {
                    if (!empty(trim($termName))) {
                        $hasTerms = true;
                        $percentage = isset($percentages[$index]) ? floatval($percentages[$index]) : 0;
                        $totalPercentage += $percentage;
                    }
                }
            }
        }

        if (!$hasTerms) {
            return back()->withErrors(['terms' => 'At least one term must be provided.'])->withInput();
        }

        if (abs($totalPercentage - 100) > 0.01) {
            return back()->withErrors(['percentages' => 'The total percentage must equal 100%. Current total: ' . number_format($totalPercentage, 2) . '%'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the appraisal configuration
            $appraisalConf = hr_appraisal_conf::create([
                'job_id' => $request->input('job_id'),
            ]);

            $termIds = [];

            // Process terms for each category
            foreach (['KPIs', 'discipline', 'efficiency'] as $category) {
                $terms = $request->input("terms.{$category}", []);
                $percentages = $request->input("percentages.{$category}", []);
                
                foreach ($terms as $index => $termName) {
                    $termName = trim($termName);
                    if (!empty($termName)) {
                        $percentage = isset($percentages[$index]) ? floatval($percentages[$index]) : 0;
                        // Check if term already exists
                        $existingTerm = hr_appraisal_terms_conf::where('term_name', $termName)
                            ->where('category', $category)
                            ->first();

                        if ($existingTerm) {
                            // Update existing term with new percentage
                            $existingTerm->update(['percentage' => $percentage]);
                            $termIds[] = $existingTerm->id;
                        } else {
                            // Create new term
                            $newTerm = hr_appraisal_terms_conf::create([
                                'category' => $category,
                                'term_name' => $termName,
                                'percentage' => $percentage,
                            ]);
                            $termIds[] = $newTerm->id;
                        }
                    }
                }
            }

            // Attach all terms to the appraisal configuration
            if (!empty($termIds)) {
                $appraisalConf->terms()->attach($termIds);
            }

            DB::commit();

            // Redirect with success message
            return redirect()->route('hr_appraisal_conf.index')->with('success', 'Appraisal configuration created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating appraisal configuration: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $appraisalConf = hr_appraisal_conf::with('terms')->findOrFail($id);
        $jobs = jobs::all();
        
        // Group terms by category for the view
        $termsByCategory = [
            'KPIs' => [],
            'discipline' => [],
            'efficiency' => []
        ];

        foreach ($appraisalConf->terms as $term) {
            $termsByCategory[$term->category][] = $term->term_name;
        }

        return view('hr.appraisal.settings.appraisal_conf.edit', compact('appraisalConf', 'jobs', 'termsByCategory'));
    }

    public function update(Request $request, $id)
    {
        $appraisalConf = hr_appraisal_conf::findOrFail($id);
        
        $request->validate([
            'job_id' => 'required|exists:hr_jobs,id',
            'terms.KPIs' => 'array',
            'terms.discipline' => 'array',
            'terms.efficiency' => 'array',
            'terms.KPIs.*' => 'nullable|string|max:255',
            'terms.discipline.*' => 'nullable|string|max:255',
            'terms.efficiency.*' => 'nullable|string|max:255',
        ]);

        // Check if at least one term is provided
        $hasTerms = false;
        foreach (['KPIs', 'discipline', 'efficiency'] as $category) {
            if (!empty($request->input("terms.{$category}"))) {
                foreach ($request->input("terms.{$category}") as $termName) {
                    if (!empty(trim($termName))) {
                        $hasTerms = true;
                        break 2;
                    }
                }
            }
        }

        if (!$hasTerms) {
            return back()->withErrors(['terms' => 'At least one term must be provided.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Update the appraisal configuration
            $appraisalConf->update([
                'job_id' => $request->input('job_id'),
            ]);

            $termIds = [];

            // Process terms for each category
            foreach (['KPIs', 'discipline', 'efficiency'] as $category) {
                $terms = $request->input("terms.{$category}", []);
                $percentages = $request->input("percentages.{$category}", []);
                
                foreach ($terms as $index => $termName) {
                    $termName = trim($termName);
                    if (!empty($termName)) {
                        $percentage = isset($percentages[$index]) ? floatval($percentages[$index]) : 0;
                        // Check if term already exists
                        $existingTerm = hr_appraisal_terms_conf::where('term_name', $termName)
                            ->where('category', $category)
                            ->first();

                        if ($existingTerm) {
                            // Update existing term with new percentage
                            $existingTerm->update(['percentage' => $percentage]);
                            $termIds[] = $existingTerm->id;
                        } else {
                            // Create new term
                            $newTerm = hr_appraisal_terms_conf::create([
                                'category' => $category,
                                'term_name' => $termName,
                                'percentage' => $percentage,
                            ]);
                            $termIds[] = $newTerm->id;
                        }
                    }
                }
            }

            // Sync the terms (this will replace existing terms)
            $appraisalConf->terms()->sync($termIds);

            DB::commit();

            // Redirect with success message
            return redirect()->route('hr_appraisal_conf.index')->with('success', 'Appraisal configuration updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error updating appraisal configuration: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            \Log::info('Attempting to delete appraisal configuration with ID: ' . $id);
            
            $appraisalConf = hr_appraisal_conf::findOrFail($id);
            \Log::info('Appraisal configuration found for job ID: ' . $appraisalConf->job_id);
            
            // Detach all terms first
            $appraisalConf->terms()->detach();
            
            $appraisalConf->delete();
            \Log::info('Appraisal configuration deleted successfully');
            
            return response()->json(['success' => true, 'message' => 'Appraisal configuration deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting appraisal configuration: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting appraisal configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get_appraisal_conf()
    {
        $appraisalConf = hr_appraisal_conf::with(['job', 'terms']);

        return DataTables::of($appraisalConf)
            ->addColumn('job_name', function ($appraisalConf) {
                return $appraisalConf->job ? (app()->getLocale() === 'ar' ? $appraisalConf->job->name_ar : $appraisalConf->job->name_en) : 'N/A';
            })
            ->addColumn('terms_count', function ($appraisalConf) {
                return $appraisalConf->terms->count();
            })
            ->addColumn('terms_list', function ($appraisalConf) {
                return $appraisalConf->terms->pluck('term_name')->implode(', ');
            })
            ->editColumn('created_at', function ($appraisalConf) {
                return Carbon::parse($appraisalConf->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($appraisalConf) {
                $encryptedId = Crypt::encryptString($appraisalConf->id);
                $deleteFunction = 'deleteFunction(' . $appraisalConf->id . ')';

                return '
                <a href="' . route('hr_appraisal_conf.edit', ['id' => $appraisalConf->id]) . '" class="btn btn-warning btn-sm">' . trans('buttons.edit') . '</a>
                <a href="javascript:void(0)" onclick="' . $deleteFunction . '" class="btn btn-danger btn-sm delete-btn">' . trans('buttons.delete') . '</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
} 