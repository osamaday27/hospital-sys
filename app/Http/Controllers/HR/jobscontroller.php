<?php

namespace App\Http\Controllers\hr;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\models\hr\section; 
use App\models\hr\Employee;
use App\models\hr\jobs;
use App\models\hr\hr_contracts;
use App\Traits\web\file_storage;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class jobscontroller extends Controller
{
    use file_storage;

    public function index()

    {
        return view('hr.settings.jobs.index');
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        $employees = Employee::all();
        $sections = section::all();
        return view('hr.settings.jobs.create', compact('employees', 'sections'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name_en' => 'required|string|max:255|unique:hr_jobs,name_en',
            'name_ar' => 'required|string|max:255|unique:hr_jobs,name_ar',
            'section_id' => 'required|exists:hr_sections,id',
            // Additional validation for file uploads can be added if necessary
        ]);
    

        jobs::create([
            'name_en' => $request->input('name_en'),
            'name_ar' => $request->input('name_ar'),
            'section_id' => $request->input('section_id'),
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_jobs.index')->with('success', 'Department created successfully!');
    }

    public function edit($id)
    {
      $job_id=jobs::find($id);
      $sections = section::all();
      return view('hr.settings.jobs.edit',compact('job_id', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $job_id = jobs::find($id);
        $request->validate([
            'name_en' => 'required|string|max:255|unique:hr_jobs,name_en,' . $id,
            'name_ar' => 'required|string|max:255|unique:hr_jobs,name_ar,' . $id,
            'section_id' => 'required|exists:hr_sections,id',
            // Additional validation for file uploads can be added if necessary
        ]);
    
    
        // Create the contract if validation passes
        $job_id->update([
            'name_en' => $request->input('name_en'),
            'name_ar' => $request->input('name_ar'),
            'section_id' => $request->input('section_id'),
        ]);
    
        // Redirect with success message
        return redirect()->route('hr_jobs.index')->with('success', 'Job updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function test($id)
    {
        return response()->json(['success' => true, 'message' => 'Test route working for job ID: ' . $id]);
    }

    public function destroy($id)
    {
        try {
            \Log::info('Attempting to delete job with ID: ' . $id);
            
            $job = jobs::findOrFail($id);
            \Log::info('Job found: ' . $job->name_en);
            
            // Check if there are any contracts associated with this job
            $contractsCount = hr_contracts::where('job_id', $id)->count();
            \Log::info('Contracts count: ' . $contractsCount);
            
            if ($contractsCount > 0) {
                \Log::warning('Cannot delete job - contracts exist');
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot delete job. There are ' . $contractsCount . ' contract(s) associated with this job. Please delete the contracts first.'
                ], 400);
            }
            
            $job->delete();
            \Log::info('Job deleted successfully');
            
            return response()->json(['success' => true, 'message' => 'Job deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting job: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting job: ' . $e->getMessage()
            ], 500);
        }
    }
  

    public function get_jobs()
    {
        $jobs = jobs::with('sections');

        return DataTables::of($jobs)
            ->addColumn('status', function ($jobs) {
                // تحديد الحالة باللغة العربية
                $status = $jobs->status;
                switch ($status) {
                    case 'active':
                        return '<span class="badge bg-success">' . trans('status.active') . '</span>';
                    case 'inactive':
                        return '<span class="badge bg-warning">' . trans('status.inactive') . '</span>';
                    case 'temporarily_suspended':
                        return '<span class="badge bg-danger">' . trans('status.temporarily_suspended') . '</span>';
                    default:
                        return '<span class="badge bg-secondary">' . trans('status.unknown') . '</span>';
                }
            })
            ->editColumn('created_at', function ($jobs) {
                // تنسيق التاريخ (يوم شهر سنة الساعة:الدقائق ص/م)
                return Carbon::parse($jobs->created_at)->format('d/m/Y h:i A');
            })

            ->addColumn('sections', function ($jobs) {
                return $jobs->sections ? $jobs->sections->name_ar : 'N/A'  ;
            })

            // ->addColumn('employees', function ($jobs) {
            //     return $jobs->employees ? App::getLocale() === 'ar' ? $jobs->employees->name_ar : $jobs->employees->name_en : 'N/A';
            // })

            ->addColumn('action', function ($jobs) {
                $encryptedId = Crypt::encryptString($jobs->id); // تشفير الـ ID
                $deleteFunction = 'deleteFunction(' . $jobs->id . ')'; // تصحيح الاستدعاء

                // إعادة رابط الأزرار مع استدعاء الدالة للحذف
                return '
            <a href="' .
                    route('hr_jobs.edit', ['id' => $jobs->id]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
            
            <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'sections',  'action'])
            ->make(true);
    }


}