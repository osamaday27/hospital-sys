<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountry;
use App\models\operations_settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class operations_settingsController extends Controller
{
    public function index()
    {
        $operations = operations_settings::all();
        return view('settings.medical_data.operations.index', compact('operations'));
    }

    public function store(StoreCountry $request)
    {
        if (
            operations_settings::where('name->ar', $request->name_ar)
                ->orwhere('name->en', $request->name_en)
                ->exists()
        ) {
            return redirect()
                ->back()
                ->withErrors([trans('Country_trans.existes')]);
        }
        try {
            $validated = $request->validated();
            $operations = new operations_settings();
            $operations->name = ['en' => $request->name_en, 'ar' => $request->name_ar];
            $operations->description = $request->description;
            $operations->preparations = $request->preparations;
            $operations->active = 1;
            $operations->user_add = Auth::user()->id;
            $operations->account_id = Auth::user()->id_account;
            $operations->save();
            session()->flash('add');
            return redirect()->route('operations_settings.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function save(Request $request)
    {
        if (
            operations_settings::where('name->ar', $request->input('name_operations'))
                ->orwhere('name->en', $request->input('name_operations'))
                ->exists()
        ) {
            return response(['exists' => 'This data already exists']);
        }
        try {
            $operations = new operations_settings();
            $operations->name = ['en' => $request->input('name_operations'), 'ar' => $request->input('name_operations')];
            $operations->active = 1;
            $operations->user_add = Auth::user()->id;
            $operations->account_id = Auth::user()->id_account;
            $operations->save();
            return response(['success' => 'operations Test created successfully.']);
        } catch (\Exception $e) {
            return response(['mas' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $operations = operations_settings::find($id);
        return view('settings.medical_data.operations.edit', compact('operations'));
    }

    public function update(StoreCountry $request)
    {
        try {
            $validated = $request->validated();
            $operations_settings = operations_settings::findorFail($request->id);
            $operations_settings->update([($operations_settings->name = ['en' => $request->name_en, 'ar' => $request->name_ar]), ($operations_settings->description = $request->description), ($operations_settings->preparations = $request->preparations)]);
            session()->flash('uploaded_m');
            return redirect()->route('operations_settings.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $operations_settings = operations_settings::findorFail($request->id)->delete();
            session()->flash('delete_m');
            return redirect()->route('operations_settings.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
      public function update_favorite($id)
  {
        try
        {
        $favorite='';
        $operations_settings= operations_settings::findorFail($id);
        if($operations_settings->favorite==1)
        {
          $favorite='0';
        }else
        {
           $favorite='1';
        }
        $operations_settings->update([$operations_settings->favorite=$favorite]);
        return response(['success' => 'Lab Test created successfully.']);

        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
        }

  }
}

?>