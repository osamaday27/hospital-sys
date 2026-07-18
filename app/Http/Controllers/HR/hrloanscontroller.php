<?php

namespace App\Http\Controllers\hr;

use App\models\hr\hr_loans;
use App\models\hr\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class hrloanscontroller extends Controller
{
    public function index()
    {
        return view('hr.payroll.programs.loans.index');
    }

    public function create()
    {
        $employees = Employee::all();
        return view('hr.payroll.programs.loans.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_type' => 'required|in:instant,loan',
            'amount' => 'required|integer|min:1',
            'loan_date' => 'required|date',
            'no_installments' => 'required_if:loan_type,loan|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $loan = hr_loans::create([
            'employee_id' => $request->employee_id,
            'loan_type' => $request->loan_type,
            'amount' => $request->amount,
            'loan_date' => $request->loan_date,
            'no_installments' => $request->loan_type === 'instant' ? 1 : $request->no_installments,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('hr_loans.index')
            ->with('success', 'Loan request created successfully!');
    }

    public function edit($id)
    {
        $loan = hr_loans::findOrFail($id);
        $employees = Employee::all();
        return view('hr.payroll.programs.loans.edit', compact('loan', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_type' => 'required|in:instant,loan',
            'amount' => 'required|integer|min:1',
            'loan_date' => 'required|date',
            'no_installments' => 'required_if:loan_type,loan|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $loan = hr_loans::findOrFail($id);
        $loan->update([
            'employee_id' => $request->employee_id,
            'loan_type' => $request->loan_type,
            'amount' => $request->amount,
            'loan_date' => $request->loan_date,
            'no_installments' => $request->loan_type === 'instant' ? 1 : $request->no_installments,
            'notes' => $request->notes
        ]);

        return redirect()->route('hr_loans.index')
            ->with('success', 'Loan updated successfully!');
    }

    public function destroy($id)
    {
        $loan = hr_loans::findOrFail($id);
        $loan->delete();

        return redirect()->route('hr_loans.index')
            ->with('success', 'Loan deleted successfully!');
    }

    public function approve($id)
    {
        try {
            \Log::info('=== LOAN APPROVAL ATTEMPT === ID: ' . $id);
            
            $loan = hr_loans::findOrFail($id);
            \Log::info('Loan found: ID=' . $loan->id . ', Current Status=' . $loan->status);
            
            $loan->update(['status' => 'approved']);
            \Log::info('Loan status updated to approved');

            return response()->json([
                'success' => true,
                'message' => 'Loan approved successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving loan: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving loan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        $loan = hr_loans::findOrFail($id);
        $loan->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Loan rejected successfully!'
        ]);
    }

    public function show($id)
    {
        $loan = hr_loans::with('employee')->findOrFail($id);

        return view('hr.payroll.programs.loans.show', compact('loan'));
    }


    public function get_loans()
    {
        $loans = hr_loans::with('employee')->select('hr_loans.*');

        return DataTables::of($loans)
            ->addColumn('employee_name', function ($loan) {
                return $loan->employee->full_name ?? 'N/A';
            })
            ->addColumn('loan_type_text', function ($loan) {
                return $loan->loan_type === 'instant' 
                    ? trans('hr.instant_loan') 
                    : trans('hr.installment_loan');
            })
            // Enable searching by related employee name
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%{$keyword}%");
                });
            })
            // Enable searching by localized loan type text
            ->filterColumn('loan_type_text', function ($query, $keyword) {
                $keywordLower = mb_strtolower($keyword);
                $instantText = mb_strtolower(trans('hr.instant_loan'));
                $installmentText = mb_strtolower(trans('hr.installment_loan'));

                $types = [];
                if (mb_stripos($instantText, $keywordLower) !== false || mb_stripos('instant', $keywordLower) !== false) {
                    $types[] = 'instant';
                }
                if (mb_stripos($installmentText, $keywordLower) !== false || mb_stripos('installment', $keywordLower) !== false || mb_stripos('loan', $keywordLower) !== false) {
                    $types[] = 'loan';
                }
                if (!empty($types)) {
                    $query->whereIn('loan_type', $types);
                }
            })
            // Enable searching by localized status badge text
            ->filterColumn('status', function ($query, $keyword) {
                $keywordLower = mb_strtolower($keyword);
                $statuses = ['pending', 'approved', 'rejected', 'completed'];
                $matched = [];
                foreach ($statuses as $status) {
                    $text = mb_strtolower(trans('hr.loan_status_' . $status));
                    if (mb_stripos($text, $keywordLower) !== false || mb_stripos($status, $keywordLower) !== false) {
                        $matched[] = $status;
                    }
                }
                if (!empty($matched)) {
                    $query->whereIn('status', $matched);
                }
            })
            
            ->editColumn('amount', function ($loan) {
                return number_format($loan->amount) . ' ' . trans('hr.currency');
            })
            ->editColumn('installment_value', function ($loan) {
                return number_format($loan->installment_value, 2) . ' ' . trans('hr.currency');
            })
            ->editColumn('loan_date', function ($loan) {
                return Carbon::parse($loan->loan_date)->format('d/m/Y');
            })
            ->editColumn('status', function ($loan) {
                $badgeClass = [
                    'pending' => 'bg-warning',
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    'completed' => 'bg-info'
                ][$loan->status] ?? 'bg-secondary';
                
                return '<span class="badge ' . $badgeClass . '">' . trans('hr.loan_status_' . $loan->status) . '</span>';
            })
            ->addColumn('action', function ($loan) {
                $approveBtn = '';
                $rejectBtn = '';
                
                if ($loan->status === 'pending') {
                    $approveBtn = '<button class="btn btn-sm btn-success approve-btn me-1" 
                                  data-id="' . $loan->id . '">
                                  <i class="ri-check-line"></i> ' . trans('hr.approve') . '
                                  </button>';
                    
                    $rejectBtn = '<button class="btn btn-sm btn-danger reject-btn me-1" 
                                 data-id="' . $loan->id . '">
                                 <i class="ri-close-line"></i> ' . trans('hr.reject') . '
                                 </button>';
                }
                
                $showBtn = '<a href="' . route('hr_loans.show', $loan->id) . '" 
                            class="btn btn-sm btn-primary me-1">
                            <i class="ri-show-line"></i> ' . trans('hr.show') . '
                            </a>';
                            
                $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" 
                              onclick="deleteFunction(' . $loan->id . ')">
                              <i class="ri-delete-bin-line"></i> ' . trans('hr.delete') . '
                              </button>';
                
                return '<div class="d-flex">' . $approveBtn . $rejectBtn . $showBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}