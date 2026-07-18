<?php
namespace App\Http\Controllers\general;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storeclinic;
use App\models\clinic_settings;
use App\models\specialization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class clinic_settingsController extends Controller
{

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $clinic_settings=clinic_settings::where('account_id',(Auth::user()->id_account))->get();
    return view('settings.clinic.clinic_settings',compact('clinic_settings'));

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    $specialty=specialization::where('account_id',(Auth::user()->id_account))->get();
    return view('settings.clinic.clinic_add',compact('specialty'));

  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Storeclinic $request)
  {
    

   
   
    try
    {
        $clinic_setting=new clinic_settings();
        $clinic_setting->clinic_name=['en'=>$request->name_en,'ar'=>$request->name_ar];
        $clinic_setting->clinic_email=$request->email_clinic;
        $clinic_setting->clinic_phone=$request->clinic_phone;
        $clinic_setting->clinic_number_center=$request->clinic_number_center;
        $clinic_setting->clinic_address=$request->clinic_address;
        $clinic_setting->clinic_specialty=$request->clinic_specialty;
        $clinic_setting->clinic_branch=$request->clinic_branch;
        $clinic_setting->clinic_manager=$request->clinic_manager;
        $clinic_setting->clinic_notes=$request->clinic_notes;
        $clinic_setting->default_branch=false;
        $clinic_setting->account_id=(Auth::user()->id_account);
        $clinic_setting->id_user=(Auth::user()->id);
        $clinic_setting->save();
        session()->flash('add');
        return redirect()->route('clinic_settings.index');
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
    $clinic_data=clinic_settings::find($id);
    return view('settings.clinic.clinic_edit',compact('clinic_data'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Storeclinic $request)
  {
        try
        {
        $validated = $request->validated();
        $clinic_setting= clinic_settings::findorFail($request->id);
        $clinic_setting->update([
          $clinic_setting->clinic_name=['en'=>$request->name_en,'ar'=>$request->name_ar],
          $clinic_setting->clinic_email=$request->email_clinic,
          $clinic_setting->clinic_phone=$request->clinic_phone,
          $clinic_setting->clinic_number_center=$request->clinic_number_center,
          $clinic_setting->clinic_address=$request->clinic_address,
          $clinic_setting->clinic_specialty=$request->clinic_specialty,
          $clinic_setting->clinic_branch=$request->clinic_branch,
          $clinic_setting->clinic_manager=$request->clinic_manager,
          $clinic_setting->clinic_notes=$request->clinic_notes,
        ]);
        session()->flash('edit_m');
        return redirect()->route('clinic_settings.index');

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

      $clinic_settings= clinic_settings::findorFail($request->id)->delete();
       session()->flash('edit_m');
        return redirect()->route('clinic_settings.index');

    }
    catch(\Exception $e)
    {
        return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
    }
  }

}

?>
