<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storedrugs_settings;
use App\models\medication_settings;
use App\models\units_settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class drug_settingsController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
      $drugs_settings=medication_settings::all();
      $units_settings=units_settings::all();
    return view('settings.medical_data.medication.index',compact('drugs_settings','units_settings'));

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {

  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Storedrugs_settings $request)
  {
    

   
    if(medication_settings::where('name->ar',$request->name_ar)->orwhere('name->en',$request->name_en)->exists())
    {
        return  redirect()->back()->withErrors([trans('drugs_settings_trans.existes') ]);
    }
    try
    {
        $validated = $request->validated();
        $drugs_settings=new medication_settings();
        $drugs_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $drugs_settings->effective_material=$request->effective_material;
        $drugs_settings->description=$request->description;
        $drugs_settings->preparations=$request->preparations;
        $drugs_settings->default_dose_unit=$request->default_dose_unit;
        $drugs_settings->default_dose=$request->default_dose;
        $drugs_settings->method_taking_medications=$request->method_taking_medications;
        $drugs_settings->start_dose=$request->start_dose;
        $drugs_settings->end_dose=$request->end_dose;
        $drugs_settings->duration=$request->duration;
        $drugs_settings->repeat=$request->repeat;
        $drugs_settings->user_add=(Auth::user()->id);
        $drugs_settings->account_id=(Auth::user()->id_account);
        $drugs_settings->save();
        return redirect()->route('drug_settings.index')->with('success', trans('message_trans.save'));

       
    }catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
   
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
     $medication=medication_settings::find($id);
     $units_settings=units_settings::all();
    return view('settings.medical_data.medication.edit',compact('medication','units_settings'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storedrugs_settings $request)
  {
        try
        {
        $drugs_settings= medication_settings::findorFail(1);
              $drugs_settings->update([
              $drugs_settings->name=['en'=>$request->name_en,'ar'=>$request->name_ar],
              $drugs_settings->effective_material=$request->effective_material,
              $drugs_settings->description=$request->description,
              $drugs_settings->preparations=$request->preparations,
              $drugs_settings->default_dose_unit=$request->default_dose_unit,
              $drugs_settings->default_dose=$request->default_dose,
              $drugs_settings->method_taking_medications=$request->method_taking_medications,
              $drugs_settings->start_dose=$request->start_dose,
              $drugs_settings->end_dose=$request->end_dose,
              $drugs_settings->duration=$request->duration,
              $drugs_settings->repeat=$request->repeat,
             ]);
        return redirect()->route('drug_settings.index')->with('success', trans('message_trans.updated'));

        }
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
        }

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Request $request)
  {
    try
    {
      $drugs_settings= medication_settings::findorFail($request->id)->delete();
      return redirect()->route('drug_settings.index')->with('success', trans('message_trans.delete'));
    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>
