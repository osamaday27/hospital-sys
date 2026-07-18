<?php
namespace App\Http\Controllers\general;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Controller;
use App\models\administrative\how_to_identify_place;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class how_to_identify_placecontroller extends Controller
{
    public function index()
    {
        $how_to_identify_place = how_to_identify_place::all();
        return view('general.how_to_identify_place.index', compact('how_to_identify_place'));
    }

    public function create()
    {
        return view('general.how_to_identify_place.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
        ]);
        how_to_identify_place::create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'user_add' => Auth::id(),
        ]);
        return redirect()->route('how_to_identify_place_settings.index')->with('success', 'تمت إضافة الوظيفة بنجاح.');
    }

    public function edit($id)
    {
        $how_to_identify_place = how_to_identify_place::find($id);
        return view('general.how_to_identify_place.edit', compact('how_to_identify_place'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'status' => 'required|boolean',
        ]);
        $how_to_identify_place = how_to_identify_place::find($request->id);

        $how_to_identify_place->update([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('how_to_identify_place_settings.index')->with('success', 'تم تعديل الوظيفة بنجاح.');
    }

    public function destroy($id)
    {
        $how_to_identify_place = how_to_identify_place::find($id);
        $how_to_identify_place->delete();
        return redirect()->route('how_to_identify_place_settings.index')->with('success', 'تم حذف الوظيفة بنجاح.');
    }
}