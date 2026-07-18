<?php
namespace App\Http\Controllers\medical\doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\models\medical\mdgeneralexamination;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class mdgeneralexaminationcontroller extends Controller
{
    public function index($patient_id)
    {
        if ($patient_id) {
            $examinations = mdgeneralexamination::where('patient_id', $patient_id)->with('user')->latest();

            return DataTables::of($examinations)
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('systems_review', function ($row) {
                    $systems = [
                        'head_neck' => ['label' => 'Head & Neck', 'comment' => $row->comment_head_neck],
                        'chest' => ['label' => 'Chest', 'comment' => $row->comment_chest],
                        'abdomen' => ['label' => 'Abdomen', 'comment' => $row->comment_abdomen],
                        'cns' => ['label' => 'CNS', 'comment' => $row->comment_cns],
                        'heart' => ['label' => 'Heart', 'comment' => $row->comment_heart],
                        'git' => ['label' => 'GIT', 'comment' => $row->comment_git],
                        'edema' => ['label' => 'Edema', 'comment' => $row->comment_edema],
                        'other' => ['label' => 'Others', 'comment' => $row->comment_other],
                    ];

                    $output = '<ul>';
                    foreach ($systems as $key => $system) {
                        if ($row->$key) {
                            $output .= "<li><strong>{$system['label']}:</strong> " . ($system['comment'] ?? '-') . '</li>';
                        }
                    }
                    $output .= '</ul>';

                    return $output !== '<ul></ul>' ? $output : '-';
                })
                ->addColumn('formatted_created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '-';
                })

                ->rawColumns(['systems_review']) // السماح بعرض الـ HTML في DataTables
                ->make(true);
        }

        return view('examinations.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'visit_id' => 'required|exists:patient_visits,id',
            'general_appearance' => 'nullable|string',
            'head_neck' => 'boolean',
            'comment_head_neck' => 'nullable|string',
            'chest' => 'boolean',
            'comment_chest' => 'nullable|string',
            'abdomen' => 'boolean',
            'comment_abdomen' => 'nullable|string',
            'cns' => 'boolean',
            'comment_cns' => 'nullable|string',
            'heart' => 'boolean',
            'comment_heart' => 'nullable|string',
            'git' => 'boolean',
            'comment_git' => 'nullable|string',
            'edema' => 'boolean',
            'comment_edema' => 'nullable|string',
            'other' => 'nullable|string',
            'comment_other' => 'nullable|string',
        ]);

        $examination = mdgeneralexamination::create([
            'patient_id' => $request->patient_id,
            'visit_id' => $request->visit_id,
            'general_appearance' => $request->general_appearance,
            'head_neck' => $request->head_neck ?? false,
            'comment_head_neck' => $request->comment_head_neck,
            'chest' => $request->chest ?? false,
            'comment_chest' => $request->comment_chest,
            'abdomen' => $request->abdomen ?? false,
            'comment_abdomen' => $request->comment_abdomen,
            'cns' => $request->cns ?? false,
            'comment_cns' => $request->comment_cns,
            'heart' => $request->heart ?? false,
            'comment_heart' => $request->comment_heart,
            'git' => $request->git ?? false,
            'comment_git' => $request->comment_git,
            'edema' => $request->edema ?? false,
            'comment_edema' => $request->comment_edema,
            'other' => $request->other,
            'comment_other' => $request->comment_other,
            'user_id' => auth()->id(), // إضافة المستخدم تلقائيًا
        ]);

        return response()->json(['message' => 'تم حفظ الفحص العام بنجاح', 'data' => $examination], 201);
    }
}