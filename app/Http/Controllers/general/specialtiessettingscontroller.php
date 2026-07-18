<?php
namespace App\Http\Controllers\general;

use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\models\general\specialties;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Traits\web\file_storage;

class specialtiessettingscontroller extends Controller
{
    use file_storage;
    // Display a listing of the resource
    public function index()
    {
        return view('general.specialties.index');
    }

    // Show the form for creating a new resource
    public function create()
    {
        return view('general.specialties.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $specialtyData = $request->all();
        if ($request->hasFile('logo')) {
            $specialtyData['logo'] = $this->file_storage($request->file('logo'), 'specialties_img');
        }

        specialties::create($specialtyData);

        return redirect()->route('specialties_settings.index')->with('success', trans('general_trans.added_successfully'));
    }

    // Display the specified resource
    public function show(specialties $specialty)
    {
        return view('general.specialties.show', compact('specialty'));
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $specialty = specialties::findOrFail($id);
        return view('general.specialties.edit', compact('specialty'));
    }

    // Update the specified resource in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $specialty = specialties::findOrFail($id);
        $specialtyData = $request->all();

        if ($request->hasFile('logo')) {
            $specialtyData['logo'] = $this->file_storage($request->file('logo'), 'specialties_img');
        }

        $specialty->update($specialtyData);

        return redirect()->route('specialties_settings.index')->with('success', 'Specialty updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $specialty = specialties::findOrFail($id);
            $specialty->delete();
            return response()->json(['success' => 'Specialty deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getspecialties()
    {
        $specialties = specialties::get();

        return DataTables::of($specialties)
            ->addColumn('status', function ($specialty) {
                return '<span class="badge bg-success">' . trans('status.active') . '</span>';
            })
            ->editColumn('created_at', function ($specialty) {
                return Carbon::parse($specialty->created_at)->format('d/m/Y h:i A');
            })
            ->addColumn('logo', function ($specialty) {
                // تأكد من أن لديك عمود logo يحتوي على المسار الصحيح للصورة
                $logoUrl = asset($specialty->logo); // تعديل المسار بناءً على مكان تخزين الصورة
                return '<img src="' . $logoUrl . '" alt="' . $specialty->name_en . '" class="img-thumbnail" width="50">';
            })
            ->addColumn('action', function ($specialty) {
                $encryptedId = Crypt::encryptString($specialty->id);
                $deleteFunction = 'deleteFunction(' . $specialty->id . ')';

                return '
                <a href="' .
                    route('specialties_settings.edit', ['specialty' => $encryptedId]) .
                    '" class="btn btn-warning btn-sm">' .
                    trans('buttons.edit') .
                    '</a>
                <a href="javascript:void(0)" onclick="' .
                    $deleteFunction .
                    '" class="btn btn-danger btn-sm delete-btn">' .
                    trans('buttons.delete') .
                    '</a>';
            })
            ->rawColumns(['status', 'logo', 'action']) // إضافة عمود 'logo' لتتم معالجته كـ raw
            ->make(true);
    }
}