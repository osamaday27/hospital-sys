<?php
namespace App\Http\Controllers\general;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Controller;
use App\models\administrative\jops;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class jopcontroller extends Controller
{
    public function index()
    {
        $jobs = jops::all();
        return view('general.jop.index', compact('jobs'));
    }

    public function create()
    {
        return view('general.jop.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
        ]);
        jops::create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'user_add' => Auth::id(),
        ]);
        return redirect()->route('jop_settings.index')->with('success', 'تمت إضافة الوظيفة بنجاح.');
    }

    public function edit($id)
    {
        $jops = jops::find($id);
        return view('general.jop.edit', compact('jops'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'status' => 'required|boolean',
        ]);
        $jops = jops::find($request->id);

        $jops->update([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('jop_settings.index')->with('success', 'تم تعديل الوظيفة بنجاح.');
    }

    public function destroy($id)
    {
        $jops = jops::find($id);
        $jops->delete();
        return redirect()->route('jop_settings.index')->with('success', 'تم حذف الوظيفة بنجاح.');
    }
}